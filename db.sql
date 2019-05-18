create table traffic(
    id int not null auto_increment primary key,
    ip varchar(20),
    longitude varchar(100),
    latitude varchar(100),
    timestamp_created int unsigned,
    timestamp_online_limit int unsigned
)engine=MyISAM;