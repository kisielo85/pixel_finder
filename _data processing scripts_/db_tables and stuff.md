2022_official.csv
160353085 lines

create table 2022_official( date datetime, x int, y int, color varchar(6), hash varchar(88) );
LOAD DATA INFILE '/tmp/2022_official.csv' INTO TABLE 2022_official FIELDS TERMINATED BY ',';
create index idx_date on 2022_official(date);
create index idx_username on 2022_official (username);


2022_opl
114492522 lines




2022_trophy
400000 lines

create table 2022_trophy(x int,y int,first_placer varchar(88),final_canvas varchar(88));
LOAD DATA INFILE '/tmp/2022_trophy.csv' INTO TABLE 2022_trophy FIELDS TERMINATED BY ',';
create index idx_first_placer on 2022_trophy(first_placer);
create index idx_final_canvas on 2022_trophy(final_canvas);