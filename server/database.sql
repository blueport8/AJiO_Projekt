create table city
(
	id serial primary key,
	name char(128) not null,
	code char(128) not null
);

create table advertisement
(
	id serial primary key,
	title text not null,
	price int not null,
	added_date timestamp not null,
	insert_date timestamp not null,
	city_id int references public.city("id"),
	rooms int,
	bathrooms int,
	size int,
	tentant char(1),
	description text not null,
	url char(1024) unique not null 
);

create table configuration_variant
(
	id serial primary key,
	code char(64) not null,
	name char(256) not null,
	creation_time timestamp not null,
	update_time timestamp not null
);

create table configuration
(
	id serial primary key,
	configuration_variant_id int references public.configuration_variant("id"),
	key char(256) not null,
	value text not null
);