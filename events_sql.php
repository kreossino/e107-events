CREATE TABLE e107_events (
  id int(10) NOT NULL auto_increment,
  event_name varchar(80) NOT NULL,
  event_datum int(10) NOT NULL,
  event_anmeldeschluss int(10) NOT NULL,
  event_details text NOT NULL,
  verfasser varchar(100) NOT NULL,
  datum int(10) NOT NULL,
  event_max_plaetze int(10) NOT NULL,
  event_ort varchar(100) NOT NULL,
  event_kosten varchar(100) NOT NULL,
  eventdetails varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE e107_events_anmeldung (
  id int(10) NOT NULL,
  member_id int(10) NOT NULL,
  event_id int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
