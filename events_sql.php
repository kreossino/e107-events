CREATE TABLE IF NOT EXISTS `events` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(80) NOT NULL,
  `event_datum` int(10) NOT NULL,
  `event_anmeldeschluss` int(10) NOT NULL,
  `event_details` text NOT NULL,
  `verfasser` varchar(100) NOT NULL,
  `datum` int(10) NOT NULL,
  `event_max_plaetze` int(10) NOT NULL,
  `event_ort` varchar(100) NOT NULL,
  `event_kosten` varchar(100) NOT NULL,
  `eventdetails` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `events_anmeldung` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL,
  `event_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;
