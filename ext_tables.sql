#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	tx_aoeipauth_ip int(11) unsigned DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'fe_groups'
#
CREATE TABLE fe_groups (
	tx_aoeipauth_ip int(11) unsigned DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'tx_aoeipauth_domain_model_ip'
#
CREATE TABLE tx_aoeipauth_domain_model_ip (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	fe_user int(11) unsigned DEFAULT '0' NOT NULL,
	fe_group int(11) unsigned DEFAULT '0' NOT NULL,
	ip varchar(255) DEFAULT '' NOT NULL,
	range_type tinyint(4) unsigned DEFAULT '0' NOT NULL,
	description varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
