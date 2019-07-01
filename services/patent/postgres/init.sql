create table users (
  row serial,
  username varchar(32),
  password varchar(32),
  secretkey varchar(32),
  permission int
);

create table sessions (
    row serial,
    username varchar(32),
    time timestamp
);

create table object_info (
  row serial,
  autor_id int,
  card_id int,
  name varchar(32),
  description varchar(128)
);

create table card (
  row int,
  cardNumber varchar(20),
  mon int,
  year int,
  cvv int,
  owner varchar(128)
);

create sequence card_seq start 1;

INSERT INTO users (row, username, password, secretkey, permission)
VALUES (0, 'root', '3A6BFF0799C7389F522F3847C33A468F', 'varysecretkey', 100);

INSERT INTO card (row, cardNumber, mon, year, cvv, owner)
VALUES (0, '1234 5678 9012 3456', 06, 2019, 030, 'AltayCTF 2019');

INSERT INTO object_info (autor_id, card_id, name, description)
VALUES (0, 0, 'Patent in the sun', 'I confirm with this patent that the sun belongs to me')
     , (0, 0, 'Patent for the moon', 'With this patent I confirm that the moon belongs to me')
     , (0, 0, 'Death Star patent', 'Luke, I am your father');
