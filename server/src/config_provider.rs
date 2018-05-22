pub mod config_provider {
    use serde_json;
    use std::fs::File;
    use std::io::prelude::*;

    pub fn load_config() -> serde_json::Value {
        let config_string = read_config_from_file();
        let config_parsed_json: serde_json::Value = serde_json::from_str(&config_string).expect("Failed to deserialize config");
        config_parsed_json
    }

    fn read_config_from_file() -> String {
        let mut file = File::open("config.json")
            .expect("Failed to open config file");

        let mut contents = String::new();
        file.read_to_string(&mut contents)
            .expect("Failed to read config file");

        contents
    }
}