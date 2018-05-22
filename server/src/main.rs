extern crate curl;
extern crate regex;
extern crate serde_json;
extern crate postgres;
extern crate ctrlc;
extern crate chrono;

pub mod config_provider;
pub mod database_provider;
use config_provider::config_provider::load_config;
use database_provider::database_provider::*;

use curl::easy::Easy;
use regex::Regex;

use std::fs::File;
use std::io::prelude::*;
use std::{thread, time};

use std::sync::atomic::{AtomicBool, Ordering};
use std::sync::Arc;

use chrono::prelude::*;

fn main() {
    println!("Gumtree parser starting...");
    println!("Loading configuration");
    let conf: serde_json::Value = load_config();

    println!("Loading database configuration");
    let db_conn_config = build_connection_struct(&conf);

    println!("Connecting to database");
    let db_connection = get_connection(db_conn_config);

    println!("Loading cities");
    let cities: Vec<City> = get_all_cities(&db_connection);
    println!("{:?}", cities);

    let running = Arc::new(AtomicBool::new(true));
    let r = running.clone();
    ctrlc::set_handler(move || {
        r.store(false, Ordering::SeqCst);
    }).expect("Error setting Ctrl-C handler");

    println!("Initializing engine");
    let conf_sleep_between_runs: u64 = conf["parser"]["sleep_between_runs"].as_str()
        .expect("Failed to load sleep sleep between runs key from configuration")
        .to_string().parse().expect("Failed to parse sleep between runs duration from config");
    let sleep_duration = time::Duration::from_millis(conf_sleep_between_runs);
    let mut last_inserted: (String, String) = (String::new(), String::new());

    while running.load(Ordering::SeqCst) {
        let mut response_results: Vec<(String, String)> = Vec::new();
        let raw_response: Vec<u8> = make_web_request(conf["parser"]["list_url"].as_str().unwrap());
        let parsed_response = parse_response(String::from_utf8(raw_response).unwrap());

        let adverts_outer_div_regex = Regex::new("<div class=\"view\">(.*)</div>").unwrap();
        for captured_outer_div in adverts_outer_div_regex.captures_iter(&parsed_response) {
            //debug_write_to_file(&captured_outer_div[0], "foo_parsed.txt").expect("a");
            let adverts_regex = Regex::new("<div class=\"title\">(.+?)</div>").unwrap();

            for captured_adverts in adverts_regex.captures_iter(&captured_outer_div[0]) {
                let advert_regex = Regex::new("href=\"(.+?{1})\">(.+?{2})</a>").unwrap();

                for advert in advert_regex.captures_iter(&captured_adverts[0]) {
                    response_results.push((advert[1].to_string(), advert[2].to_string()))
                }
            }
        }
        let response_results: Vec<(String, String, i32)> = filter_results_by_city(response_results, &cities);
        if response_results.len() > 0 {
            if last_inserted.0.is_empty() && last_inserted.1.is_empty() {
                let adv: Advertisement = get_advert(format!("https://www.gumtree.pl{}", parse_polish_chars_in_address(&response_results[0].0)), response_results[0].2);
                println!("{:?}", adv);
                last_inserted = (response_results[0].0.clone(), response_results[0].1.clone());
                println!("{:?}", last_inserted);
                insert_advert(&db_connection, adv);
            } else {
                let first_adv: (String, String, i32) = response_results[0].clone();
                for result in response_results {
                    //println!("last inserted: {}\ncurrent{}\n", last_inserted.0, result.0);
                    if last_inserted.0 == result.0 {
                        last_inserted = (first_adv.0, first_adv.1);
                        println!("\nchanged last inserted previous: {}\nnow: {}", last_inserted.0, result.0);
                        break;
                    } else {
                        let adv: Advertisement = get_advert(format!("https://www.gumtree.pl{}", parse_polish_chars_in_address(&result.0)), result.2);
                        insert_advert(&db_connection, adv);
                    }
                }
            }
        }

        println!("Sleeping");
        thread::sleep(sleep_duration);
    }

    println!("Closing database connection");
    db_connection.finish().expect("Failed to disconnect");  
}



fn parse_response(response: String) -> String {
    let response = str::replace(&response, "\n", "");
    let response = str::replace(&response, "\t", "");
    response
}

fn debug_write_to_file(value: &str, file_name: &str) -> std::io::Result<()> {
    let mut file = File::create(file_name)?;
    file.write_all(value.as_bytes())?;
    Ok(())
}

fn make_web_request(url: &str) -> Vec<u8> {
	let mut dst = Vec::new();
    {
    	let mut easy = Easy::new();
    	easy.url(url).unwrap();
    	let mut transfer = easy.transfer();
	    transfer.write_function(|data| {
	        dst.extend_from_slice(data);
	        Ok(data.len())
	    }).unwrap();
	    transfer.perform().unwrap();
    }
    dst
}

fn build_connection_struct(config: &serde_json::Value) -> ConnectionData{
    let error_form: String = String::from("Failed to load {message} key from confguration");
    let conf_addr: String = config["database"]["host"].as_str().expect(&str::replace(&error_form, "{message}", "database address")).to_string();
    let conf_user: String = config["database"]["user"].as_str().expect(&str::replace(&error_form, "{message}", "user")).to_string();
    let conf_password: String = config["database"]["password"].as_str().expect(&str::replace(&error_form, "{message}", "password")).to_string();
    let conf_port: String = config["database"]["port"].as_str().expect(&str::replace(&error_form, "{message}", "port")).to_string();
    let conf_db_name: String = config["database"]["dbname"].as_str().expect(&str::replace(&error_form, "{message}", "database name")).to_string();

    ConnectionData {
        address: conf_addr,
        user: conf_user,
        password: conf_password,
        port: conf_port,
        database_name: conf_db_name
    }
}

fn filter_results_by_city(results: Vec<(String, String)>, cities: &Vec<City>) -> Vec<(String, String, i32)> {
    let mut filered_results: Vec<(String, String, i32)> = Vec::new();
    for result in results {
        for city in cities {
            let city_code = city.code.trim();
            if result.0.contains(&city_code[..]) {
                filered_results.push((result.0, result.1, city.id));
                break;
            }
        }
    }
    filered_results
}

fn get_advert(url: String, city_id: i32) -> Advertisement {
    println!("{:?}", url);
    let raw_response: Vec<u8> = make_web_request(&url);
    let parsed_response = parse_response(String::from_utf8(raw_response).unwrap());
    //debug_write_to_file(&parsed_response, "advert_parsed.txt").expect("a");
    println!("Started matching site content");

    // TITLE
    let title_regex = Regex::new("<span class=\"myAdTitle\">(?P<title>.+?)</span>").unwrap();
    let mut title = String::new();
    if title_regex.is_match(&parsed_response) {
        let title_match = title_regex.captures(&parsed_response).unwrap();
        title = title_match["title"].to_string();
        //println!("Title: {:?}", title);
    } else {
        println!("Title not matched");
    }
    
    // PRICE
    let price_regex = Regex::new("<span class=\"amount\">(?P<price>.+?)</span>").unwrap();
    let mut price: i32 = 0;
    if price_regex.is_match(&parsed_response) {
        let price_match = price_regex.captures(&parsed_response).unwrap();
        let mut price_str = price_match["price"].to_string();
        price_str = price_str.replace("zł", "");
        price_str = price_str.replace("\u{a0}", "");
        price = price_str.trim().to_string().parse().unwrap();
    } else {
        println!("Price not matched");
    }

    // ADD TIME
    let mut add_time = String::new();
    let add_time_regex = Regex::new("<span class=\"name\">Data dodania</span><span class=\"value\">(?P<add_time>.+?)</span>").unwrap();
    if add_time_regex.is_match(&parsed_response) {
        let add_time_match = add_time_regex.captures(&parsed_response).unwrap();
        add_time = add_time_match["add_time"].to_string();
    } else {
        println!("Add time no matched");
    }
    
    // ROOM NUMBER
    let room_number_regex = Regex::new("<span class=\"name\">Liczba pokoi</span>    <span class=\"value\">(?P<room_num>\\d+?) pok").unwrap();
    // Handle case (Kawalerka lub garsoniera)
    let mut room_number: i32 = 1;
    if room_number_regex.is_match(&parsed_response)  {
        let room_number_match = room_number_regex.captures(&parsed_response).unwrap();
        room_number = room_number_match["room_num"].to_string().parse().unwrap();
    } else {
        println!("Room number not matched");
    }
    
    // TENTANT
    let tentant_regex = Regex::new("<span class=\"name\">Do wynajęcia przez</span>    <span class=\"value\">(?P<typ>.+?)</span>").unwrap();
    let mut tentant = String::new();
    if tentant_regex.is_match(&parsed_response) {
        let tentant_match = tentant_regex.captures(&parsed_response).unwrap();
        tentant = tentant_match["typ"].to_string();
    } else {
        println!("Tentant not matched");
        
    }

    // APARTMENT SIZE
    let apartment_size_regex = Regex::new("<span class=\"name\">Wielkość \\(m2\\)</span>    <span class=\"value\">(?P<apartment_size>.+?)</span>").unwrap();
    let mut apartment_size: i32 = 0;
    if apartment_size_regex.is_match(&parsed_response) {
        let apartment_size_match = apartment_size_regex.captures(&parsed_response).unwrap();
        apartment_size = apartment_size_match["apartment_size"].to_string().parse().unwrap();
    } else {
        println!("Apartment size not matched");
        
    }

    // BATHROOMS
    let bathrooms_regex = Regex::new("<span class=\"name\">Liczba łazienek</span>    <span class=\"value\">(?P<bathrooms>.+?)").unwrap();
    let mut bathrooms: i32 = 0;
    if bathrooms_regex.is_match(&parsed_response) {
        let bathrooms_match = bathrooms_regex.captures(&parsed_response).unwrap();
        bathrooms = bathrooms_match["bathrooms"].to_string().parse().unwrap();
    } else {
        println!("Bathrooms not matched");
        
    }

    // DESCRIPTION
    let description_regex = Regex::new("<span class=\"pre\"style=\"font-family: inherit; white-space: pre-wrap;\">(?P<description>.+?)</span></div></div></div><div class=\"vip").unwrap();
    let mut description = String::new();
    if description_regex.is_match(&parsed_response) {
        let description_match = description_regex.captures(&parsed_response).unwrap();
        description = description_match["description"].to_string();
    } else {
        println!("description not matched");
        
    }

    println!("Finished matching site content");
    let added_date_time: NaiveDateTime = match NaiveDateTime::parse_from_str(&format!("{} 00-00-00", &add_time), "%d/%m/%Y %H-%M-%S") {
        Ok(v) => v,
        Err(e) => {
            println!("Failed to parse added date. {}", e);
            NaiveDateTime::parse_from_str("01/01/2018 00-00-00", "%d/%m/%Y %H-%M-%S").unwrap()
        }
    };

    let tentant_symbol: char = match tentant.chars().next() {
        Some(t) => t,
        None => 'U'
    };

    Advertisement {
        id: 0,
        title,
        price: price,
        added_date: added_date_time,
        bathrooms: bathrooms,
        city_id: city_id,
        description: description,
        insert_date: Utc::now(),
        rooms: room_number,
        size: apartment_size,
        tentant: tentant_symbol,
        url: url
    }
}

fn parse_polish_chars_in_address(address: &String) -> String {
    let address = str::replace(&address, "%C4%85", "a");
    let address = str::replace(&address, "%C4%87", "c");
    let address = str::replace(&address, "%C4%99", "e");
    let address = str::replace(&address, "%C5%82", "l");
    let address = str::replace(&address, "%C5%84", "n");
    let address = str::replace(&address, "%C3%B3", "o");
    let address = str::replace(&address, "%C5%9B", "s");
    let address = str::replace(&address, "%C5%BA", "z");
    let address = str::replace(&address, "%C5%BC", "z");
    address
}
