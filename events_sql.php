CREATE TABLE IF NOT EXISTS `events` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(80) NOT NULL,
  `event_date` int(10) NOT NULL,
  `event_deadline` int(10) NOT NULL,
  `event_details` text NOT NULL,
  `event_author` varchar(100) NOT NULL,
  `event_date_create` int(10) NOT NULL,
  `event_max_places` int(10) NOT NULL,
  `event_location` varchar(100) NOT NULL,
  `event_costs` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `events_registration` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL,
  `event_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;
