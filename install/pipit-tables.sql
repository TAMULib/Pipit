
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: 'phpseedapp'
--
CREATE DATABASE IF NOT EXISTS pipit DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE pipit;

-- --------------------------------------------------------

--
-- Table structure for table 'users'
--

CREATE TABLE IF NOT EXISTS users (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(20) NOT NULL,
  `password` varchar(80) NOT NULL,
  email varchar(120) NOT NULL,
  name_first varchar(30) NOT NULL,
  name_last varchar(30) NOT NULL,
  isadmin tinyint(1) NOT NULL DEFAULT '0',
  inactive tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- Default password is 'changethis' --
INSERT INTO users (username, password, email, name_first, name_last, isadmin, inactive) VALUES
('admin', '$2y$10$XWsCnoBSNE2P6YKD3ERqZ.Wjwtq1RR5fgXKVcRYaWtmpkPGbYyi.G', '', 'Adam', 'Admin', 1, 0);


--
-- Table structure for table 'users_ldap'
--

CREATE TABLE IF NOT EXISTS users_ldap (
  id int(11) NOT NULL AUTO_INCREMENT,
  userid int(11) NOT NULL,
  samaccountname varchar(50) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY userid (userid)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'widgets'
--

CREATE TABLE IF NOT EXISTS widgets (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'widgets_parts'
--

CREATE TABLE IF NOT EXISTS widgets_parts (
  id int(11) NOT NULL AUTO_INCREMENT,
  widgetid int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY (id),
  KEY widgetid (widgetid)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;