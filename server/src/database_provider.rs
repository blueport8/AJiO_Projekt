pub mod database_provider {
	use postgres::{Connection, TlsMode};
	use chrono::prelude::*;

	#[derive(Debug)]
	pub struct City {
		pub id: i32,
		pub name: String,
		pub code: String
	}

	#[derive(Debug)]
	pub struct Advertisement {
	    pub id: i32,
	    pub title: String,
	    pub price: i32,
	    pub added_date: NaiveDateTime,
	    pub insert_date: DateTime<Utc>,
	    pub city_id: i32,
	    pub rooms: i32,
	    pub bathrooms: i32,
	    pub size: i32,
	    pub tentant: char,
	    pub description: String,
	    pub url: String
	}

	pub struct ConnectionData {
		pub address: String,
		pub port: String,
		pub user: String,
		pub password: String,
		pub database_name: String
	}

	pub fn get_connection(connection_data: ConnectionData) -> Connection {
		let connection_string: String = build_connection_string(connection_data);
		Connection::connect(connection_string, TlsMode::None).unwrap()
	}

	fn build_connection_string(connection_data: ConnectionData) -> String {
		format!("postgres://{}:{}@{}:{}/{}", 
			connection_data.user,
			connection_data.password,
			connection_data.address,
			connection_data.port,
			connection_data.database_name)
	}

	pub fn get_all_cities(conn: &Connection) -> Vec<City> {
		let mut cities: Vec<City> = Vec::new();
		for row in &conn.query("select id, name, code from city", &[]).unwrap() {
			let mut city_row = City {
	            id: row.get(0),
	            name: row.get(1),
	            code: row.get(2)
	        };
	        city_row.name = city_row.name.trim().to_string();
	        city_row.code = city_row.code.trim().to_string();
	        cities.push(city_row);
		}
		cities
	}

	pub fn insert_advert(conn: &Connection, advert: Advertisement) {
		match conn.execute("insert into advertisement (title, price, added_date, insert_date, city_id, rooms, bathrooms, size, tentant, description, url) 
			values ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11)", 
			&[&advert.title, &advert.price, &advert.added_date,  &advert.insert_date.naive_utc(),
			 &advert.city_id, &advert.rooms, &advert.bathrooms, &advert.size, &advert.tentant.to_string(), &advert.description, &advert.url]) {
			Ok(c) => {println!("Inserted succesfully, {}", c);},
			Err(a) => {println!("Duplicate, {}", a); return;}
		}
	}
}