use test;
drop table if exists item;
create table item
	(it_upc char(12) not null,
	it_title varchar(40) not null,
	type varchar(3) not null, -- Define our own type here
	category char(12) not null, -- Define our own type here
	company varchar(40) not null,
	year YEAR(4) not null,
	price DECIMAL(10,2) not null,
	stock int not null,
	PRIMARY KEY (it_upc)
	);
 
-- grant select on items to public;
 
drop table if exists leadsinger;
 
create table leadsinger
	(ls_upc char(12) not null,
	ls_name varchar(40) null,
	PRIMARY KEY (ls_upc, ls_name),
	FOREIGN KEY (ls_upc) REFERENCES item(it_upc)
	);
 
-- grant select on leadsinger to public;
 
drop table if exists hassong;
 
create table hassong
	(hs_upc char(12) not null,
	hs_title varchar(40) null,
	PRIMARY KEY (hs_upc, hs_title),
	FOREIGN KEY (hs_upc) REFERENCES item(it_upc)
	);
 
-- grant select on hassong to public;

drop table if exists customer;
 
create table customer
	(cid varchar(20) not null,
	c_password varchar(20) not null,
	c_name varchar(40) not null,
	address varchar(255) not null,
    phone char(11) not null,
    PRIMARY KEY (cid)
	);
 
-- grant select on customer to public;

drop table if exists purchase;
 
create table purchase
	(p_receiptId int not null AUTO_INCREMENT, -- What type should this be?
	p_date DATETIME not null,
	p_cid varchar(20) not null,
	cardNo char(16) not null,
	expiryDate DATE null,
    expectedDate DATE not null,
    deliveredDate DATE null,
    PRIMARY KEY (p_receiptID),
    FOREIGN KEY (p_cid) REFERENCES customer(cid)
    );
 
-- grant select on order to public;
drop table if exists purchaseitem;
 
create table purchaseitem
	(pi_receiptId int not null,
	pi_upc char(12) not null,
	pi_quantity int not null,
    PRIMARY KEY (pi_receiptId, pi_upc),
    FOREIGN KEY (pi_receiptId) REFERENCES purchase(p_receiptId),
    FOREIGN KEY (pi_upc) REFERENCES item(it_upc) 
    );
 
-- grant select on purchaseitem to public;
 
drop table if exists returnrecord;
 
create table returnrecord
	(retId int not null auto_increment,
	ret_date DATETIME not null,
	ret_receiptId int null,
    PRIMARY KEY (retId),
    FOREIGN KEY (ret_receiptId) REFERENCES purchase(p_receiptId)
    );
 
-- grant select on returnrecord to public;
 
drop table if exists returnitem;
 
create table returnitem
	(ri_retid int not null,
	ri_upc char(12) not null,
	ri_quantity int null,
    PRIMARY KEY (ri_retId, ri_upc),
    FOREIGN KEY (ri_retId) REFERENCES returnrecord(retId),
    FOREIGN KEY (ri_upc) REFERENCES item(it_upc)
	);
 
-- grant select on returnitem to public;
 
create unique index itemindex
on item (it_upc);

create unique index leadsingerindex
on leadsinger (ls_upc, ls_name);

create unique index hassongindex
on hassong (hs_upc, hs_title);

create unique index customerindex
on customer (cid);

create unique index purchaseindex
on purchase (p_receiptID);

create unique index purchaseitemindex
on purchaseitem (pi_receiptId, pi_upc);

create unique index returnrecordindex
on returnrecord (retId);

create unique index returnitemindex 
on returnitem (ri_retId, ri_upc);

ALTER TABLE item ADD FULLTEXT ft_item_ind (it_upc, it_title, type, category, company);

ALTER TABLE leadsinger ADD FULLTEXT ft_ls_ind (ls_name);
 
insert into item
values('123456789012', 'Beyonce', 'cd','pop', 'Rocafella', 2014, 9.99, 12);

insert into item
values('135658852258', 'Duotones', 'cd', 'instrumental', 'Arista', 1986, 12.99, 15);

insert into item
values('288933147766', 'Heartbreaker', 'cd', 'rap', 'YG Entertainment', 2009, 10.99, 10);

insert into item
values('123658216924', 'Memoirs of a Madman', 'dvd', 'rock', 'Sony Music Canada', 2014, 17.97, 11);

insert into item
values('258624782046', 'Christmas Countdown', 'dvd', 'pop', 'Warner Bros', 2010, 19.99, 13);

insert into item
values('246198346910', 'This is Us', 'dvd', 'pop', 'Sony Pictures', 2014, 19.99, 20);

insert into item
values('132468245973', 'Based on a True Story', 'cd', 'country', 'Warner Bros.', 2013, 12.99, 19);

insert into item
values('213469245706', 'Beethoven: Complete Piano Sonatas', 'cd', 'classical', 'Deutsche Grammophon', 1991, 43.98, 8);

insert into item
values('616402673105', 'Promised Land', 'cd', 'rock', 'RCA Victor', 1975, 6.99, 19);

insert into item
values('976135610258', 'Tears of the Moon', 'cd', 'new age', 'Symbiosis Music', 2001, 7.99, 24);
  
insert into leadsinger
values('123456789012', 'Beyonce');
 
insert into leadsinger
values('135658852258', 'Kenny');

insert into leadsinger
values('288933147766', 'G-Dragon');

insert into leadsinger
values('132468245973', 'Blake Shelton');

insert into leadsinger
values('213469245706', 'Wilhelm Kempff');

insert into leadsinger
values('616402673105', 'Elvis Presley');

insert into leadsinger
values('976135610258', 'Symbiosis');

insert into leadsinger
values('123658216924', 'Ozzy Osbourne');

insert into leadsinger
values('258624782046', 'Elmo');

insert into leadsinger
values('246198346910', 'One Direction');

insert into hassong
values('123456789012', 'My Love');

insert into hassong
values('135658852258', 'Songbird');

insert into hassong
values('288933147766', 'Butterfly');

insert into hassong
values('132468245973', 'Sure Be Cool If You Did'); 

insert into hassong
values('213469245706', 'Prestissimo');

insert into hassong
values('616402673105', 'Promised Land');

insert into hassong
values('616402673105', 'Thinking About You');

insert into hassong
values('616402673105', 'If You Talk In Your Sleep');

insert into hassong
values('976135610258', 'Water Garden');

insert into hassong
values('123658216924', 'Shot in the Dark');

insert into hassong
values('258624782046', 'Feliz Navidad');

insert into hassong
values('246198346910', 'Night Changes');

insert into customer
values('cocopuffsrule', 'cocopuffs', 'CoCo', '605 Expo Boulevard Vancouver, BC, Canada', '18883652589');

insert into customer
values('mojojojo', 'mojo', 'Monkey', '2051 S. Cole Road Show Idaho, USA', '13362578521');

insert into customer
values('powerpuffgirls', 'sugarandspice', 'Buttercup', '21300 Roscoe Blvd, California, USA', '1589654741');

insert into customer
values('tinkerbell', 'peterpansucks', 'Tinker', '16441 108A Tree, Neverneverland', '12352321025');

insert into customer
values('peterpan', 'lostboys', 'Peter', '62 Renmin Lu, Qindao shi, Shangdong, China', '23650258740');

insert into customer
values('snowman', 'buildasnowman', 'Olaf', '1929 North Pole, Edmonton, Alberta, Canada', '12589631470');

insert into purchase
values('3216', 20140701, 'cocopuffsrule', '5957156807596423', 20150301, 20140708, 20140708);

insert into purchase
values('3418', 20140901, 'cocopuffsrule', '5957156807596423', 20150301, 20140910, 20140911);

insert into purchase
values('3258', 20141006, 'peterpan', '7216734982054673', 20150301, 20141012, 20141012);

insert into purchase
values('3201', 20141006, 'powerpuffgirls', '2167308595021675', 20150301, 20141010, 20141012);

insert into purchase
values('3815', 20141016, 'cocopuffsrule', '5957156807596423', 20200301, 20141021, 20141021);

insert into purchase
values('3215', 20141115, 'mojojojo', '6197345168011258', 20150901, 20141120, 20141120);

insert into purchase
values('3925', 20141115, 'tinkerbell', '5163794851243658', 20150901, 20141120, 20141120);

insert into purchase
values('3926', 20141115, 'tinkerbell', '5163794851243658', 20150901, 20141120, 20141120);

insert into purchase
values('3219', 20141116, 'mojojojo', '5163794851243658', 20150901, 20141119, 20141119);

insert into purchase
values('3752', 20141116, 'cocopuffsrule', '5957156807596423', 20200301, 20141119, 20141119);

insert into purchase
values('3854', 20141116, 'peterpan', '7216734982054673', 20150301, 20141119, 20141120);

insert into purchase
values('3621', 20141116, 'peterpan', '7216734982054673', 20150301, 20141119, 20141120);

insert into purchase
values('3948', 20141117, 'snowman', '9521456985410254', 20150901, 20141120, 20141120);

insert into purchase
values('3058', 20141118, 'tinkerbell', '5163794851243658', 20150901, 20141121, 20141121);

insert into purchase
values('3264', 20141118, 'snowman', '9521456985410254', 20150901, 20141121, 20141121);

insert into purchase
values('3624', 20141119, 'peterpan', '7216734982054673', 20150301, 20141122, null);

insert into purchase
values('3958', 20141120, 'cocopuffsrule', '5957156807596423', 20200301, 20141125, null);

insert into purchase
values('3814', 20141121, 'powerpuffgirls', '2167308595021675', 20150301, 20141125, null);

insert into purchase
values('3001', 20141122, 'tinkerbell', '5163794851243658', 20150901, 20141126, null);

insert into purchase
values('3015', 20141122, 'tinkerbell', '5163794851243658', 20150901, 20141126, null);

insert into purchaseitem
values('3216', '123456789012', 1);

insert into purchaseitem
values('3418', '135658852258', 1);

insert into purchaseitem
values('3258','616402673105', 3);

insert into purchaseitem
values('3201','123658216924', 1);

insert into purchaseitem
values('3815','616402673105', 2);

insert into purchaseitem
values('3215','132468245973', 3);

insert into purchaseitem
values('3925','258624782046', 1);

insert into purchaseitem
values('3926', '616402673105', 1);

insert into purchaseitem
values('3219', '132468245973', 1);

insert into purchaseitem
values('3752', '258624782046', 1);

insert into purchaseitem
values('3854', '976135610258', 1);

insert into purchaseitem
values('3621', '123456789012', 1);

insert into purchaseitem
values('3948', '288933147766', 1);

insert into purchaseitem
values('3058', '246198346910', 2);

insert into purchaseitem
values('3264', '288933147766', 1);

insert into purchaseitem
values('3624', '135658852258', 3);

insert into purchaseitem
values('3958', '258624782046', 1);

insert into purchaseitem
values('3814', '976135610258', 2);

insert into purchaseitem
values('3001', '616402673105', 1);

insert into purchaseitem
values('3001', '213469245706', 3);

insert into purchaseitem
values('3001', '976135610258', 1);

insert into purchaseitem
values('3015', '976135610258', 5);

insert into returnrecord
values('2365', 20140719, '3216');

insert into returnrecord
values('2918', 20141112, '3815');

insert into returnrecord
values('2912', 20141119, '3058');

insert into returnrecord
values('2963', 20141121, '3854');

insert into returnrecord
values('2945', 20141118, '3948');

insert into returnrecord
values('2932', 20141120, '3752');

insert into returnitem
values('2365', '123456789012', 1);

insert into returnitem
values('2918', '616402673105', 2);

insert into returnitem
values('2912', '246198346910', 1);

insert into returnitem
values('2963', '976135610258', 1);

insert into returnitem
values('2945', '288933147766', 1);

insert into returnitem
values('2932', '258624782046', 1);

commit;
