drop table albums;
drop table artists;
drop table producers;

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
/*
CREATE TABLE producers
    (producerID	int not null,
    name		CHAR(30) not null,
    age	 	int null,
    numAlbumsContracted	int	NOT NULL,
    hourlyCost	FLOAT		NOT NULL,
    phoneNum	CHAR(12) null,
    PRIMARY KEY (producerID));

    grant select on producers to public;
*/
    insert into artists
    values('00001', 'Ben', '26', '2',  '100000', '654123789');

    insert into artists
    values('00012', 'Lenny',  '30', '3', '150000', '648135498');

    insert into artists
    values('01011', 'Russ',  '34', '5', '200000', '987452123');
    
    insert into albums
    values('00123', 'bootleg', '60000', '00001', '1200000');

    insert into albums
    values('01245', 'daydream', '53000', '00012', '870000');

    insert into albums
    values('15234', 'Ghost City', '120000', '01011', '2500000');
