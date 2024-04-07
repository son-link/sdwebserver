CREATE TABLE `laps` (
  `id` int(11) NOT NULL,
  `race_id` int(11) NOT NULL,
  `laptime` double NOT NULL,
  `fuel` double NOT NULL,
  `position` int(5) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `wettness` int(2) NOT NULL
) ENGINE=MyISAM;

CREATE TABLE `races` (
  `id` int(11) NOT NULL,
  `user_id` int(100) NOT NULL,
  `user_skill` int(4) NOT NULL,
  `track_id` varchar(100) NOT NULL,
  `car_id` varchar(100) NOT NULL,
  `type` int(2) NOT NULL,
  `setup` text NOT NULL,
  `startposition` int(5) NOT NULL,
  `endposition` int(5) DEFAULT NULL,
  `sdversion` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `img` varchar(100) NOT NULL,
  `nation` varchar(30) NOT NULL,
  `registrationdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `sessionid` varchar(50) DEFAULT NULL,
  `sessionip` varchar(15) DEFAULT NULL,
  `sessiontimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM;

ALTER TABLE `laps`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `races`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `laps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `races`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

CREATE TABLE IF NOT EXISTS `cars_cats` (
	`id` varchar(20) DEFAULT NULL,
	`name` varchar(50) DEFAULT NULL,
	`carID` varchar(50) DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `tracks_cats` (
	`id` varchar(20) DEFAULT NULL,
	`name` varchar(50) DEFAULT NULL,
	`trackID` varchar(50) DEFAULT NULL
) ENGINE=InnoDB;
