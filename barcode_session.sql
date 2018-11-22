create table barcode_session (
user_id varchar(32) primary key,
value varchar(255),
updated timestamp not null default current_timestamp on update current_timestamp);