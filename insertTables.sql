drop table albums;
drop table artists;

CREATE TABLE artists
    (artistID	int     not null,
    name		CHAR(30) not null,
    age	 	int null,
    numAlbumsContracted	int	NOT NULL,
    salary	FLOAT		NOT NULL,
    phoneNum	CHAR(12) null,
    PRIMARY KEY (artistID));

grant select on artists to public;

CREATE TABLE albums
    (albumID	int not null,
    albumName	CHAR(100) not null,
    sales		int	NOT NULL,
    artistID		int	NOT NULL,
    toDateRevenue	FLOAT null,
    PRIMARY KEY (albumID),
    FOREIGN KEY (artistID) REFERENCES artists
    ON DELETE CASCADE);

grant select on albums to public;

    insert into artists
    values('00001', 'Rex Orange County', '23', '2',  '1000000', '6541237899');

    insert into artists
    values('00002', 'Drake',  '38', '3', '30000000', '6481353498');

    insert into artists
    values('00003', 'Russ',  '34', '5', '5000000', '9874521123');

    insert into artists
    values('00004', 'The Weeknd',  '30', '2', '30000000', '98736579013');
    
    insert into albums
    values('00001', 'Who Cares', '50000', '00001', '1200000');

    insert into albums
    values('00002', 'Views', '200000', '00002', '20000000');

    insert into albums
    values('00003', 'Theres really a wolf', '90000', '00003', '2500000');

    insert into albums
    values('00004', 'Pony', '75000', '00001', '2000000');

    insert into albums
    values('00005', 'Certified Lover Boy', '300000', '00002', '10000000');

    insert into albums
    values('00006', 'After Hours', '485000', '00004', '15000000');
