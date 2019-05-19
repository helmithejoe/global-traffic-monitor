create table traffic(
    id int not null auto_increment primary key,
    ip varchar(20),
    longitude varchar(100),
    latitude varchar(100),
    user_agent varchar(255),
    request_uri varchar(255),
    request_method varchar(255),
    request_time int unsigned,
    timestamp_created int unsigned
)engine=MyISAM;