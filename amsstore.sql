
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
	hs_title varchar(20) null,
	PRIMARY KEY (hs_upc, hs_title),
	FOREIGN KEY (hs_upc) REFERENCES item(it_upc)
	);
 
-- grant select on hassong to public;
 
drop table if exists purchase;
 
create table purchase
	(p_receiptId int not null, -- What type should this be?
	p_date DATETIME not null,
	p_cid varchar(20) not null,
	cardNo char(16) not null,
	expiryDate DATE null,
    expectedDate DATETIME not null,
    deliveredDate DATETIME null,
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
 
drop table if exists customer;
 
create table customer
	(cid varchar(20) not null,
	c_password varchar(20) not null,
	c_name varchar(40) not null,
	address varchar(255) not null,
    phone char(12) not null,
    PRIMARY KEY (cid)
	);
 
-- grant select on customer to public;
 
drop table if exists returnrecord;
 
create table returnrecord
	(retId int not null,
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
 
--create unique index pubind on publishers
--(pub_id);
 
create unique index auidind 
on authors (au_id);
 
create index aunmind 
on authors (au_lname, au_fname);
 
create unique index titleidind 
on titles (title_id);
 
create index titleind 
on titles (title);
 
create unique index taind 
on titleauthors (au_id, title_id);
 
create unique index edind 
on editors (ed_id);
 
create index ednmind 
on editors (ed_lname, ed_fname);
 
create unique index teind 
on titleditors (ed_id, title_id);
  
create index rstidind 
on roysched (title_id);
 
insert into item
values('123456789012', 'Beyonce', 'cd',
'pop', 'Rocafella', YEAR(2014), 9.99, 5);
  
insert into leadsinger
values('123456789012', 'Beyonce');
 
insert into hassong
values('123456789012', 'My Love');
 
commit;
