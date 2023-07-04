-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 13, 2019 at 04:50 PM
-- Server version: 10.3.14-MariaDB
-- PHP Version: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `id9549775_dbmovies`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`id9549775_denim1233`@`%` PROCEDURE `PM_MovieGenre_Delete` (IN `pmoviegenreid` INT)  MODIFIES SQL DATA
BEGIN
	DELETE FROM PM_MovieGenre
    WHERE MovieGenreId = pmoviegenreid;
END$$

CREATE DEFINER=`id9549775_denim1233`@`%` PROCEDURE `PM_MovieGenre_Get` (IN `pmovieid` INT)  READS SQL DATA
BEGIN
	SELECT * FROM PM_MovieGenre
    INNER JOIN SYS_Genre ON SYS_Genre.GenreId = PM_MovieGenre.GenreId
    WHERE MovieId = pmovieid;
END$$

CREATE DEFINER=`id9549775_denim1233`@`%` PROCEDURE `PM_MovieGenre_Insert` (IN `pmoviegenreid` INT(11), IN `pmovieid` INT(11), IN `pgenreid` INT(11), IN `pmoviegenrestatus` INT(11))  MODIFIES SQL DATA
BEGIN
	IF EXISTS (SELECT GenreId from PM_MovieGenre where MovieId = pmovieid and GenreId = pgenreid)
    THEN
        SELECT 'Genre already exists in this movie' AS querystatus,'0' AS statusid;
    ELSE
        INSERT INTO PM_MovieGenre(MovieId,GenreId,MovieGenreStatus) 
        VALUES(pmovieid,pgenreid,pmoviegenrestatus);

        SELECT LAST_INSERT_ID() AS pmoviegenreid,'success' AS querystatus, '1' AS statusid;
    END IF;
END$$

CREATE DEFINER=`id9549775_denim1233`@`%` PROCEDURE `PM_Movies_Get` ()  READS SQL DATA
BEGIN
	SELECT * FROM PM_Movies
    INNER JOIN SYS_Category ON SYS_Category.CategoryId = 
    PM_Movies.MovieCategoryId
    ORDER BY PM_Movies.MovieId DESC;
END$$

CREATE DEFINER=`id9549775_denim1233`@`%` PROCEDURE `PM_Movies_ManageRecord` (IN `pmovieid` INT, IN `pmoviename` VARCHAR(100), IN `pmoviestatus` INT, IN `pmoviepicture` VARCHAR(200), IN `pmoviedescription` VARCHAR(500), IN `pmoviecategoryid` INT, IN `pmoviefeature` INT)  MODIFIES SQL DATA
BEGIN
    IF EXISTS (SELECT MovieId from PM_Movies where MovieId = pmovieid)
    THEN
    	IF EXISTS (SELECT MovieName FROM PM_Movies WHERE MovieName = pmoviename AND MovieId != pmovieid)
        THEN
        	SELECT 'Name already used by existing record' AS querystatus,'0' AS statusid;
        ELSE
            UPDATE 
            	PM_Movies 
            SET 
                MovieName = pmoviename,
                MovieStatus = pmoviestatus,
                MoviePicture = pmoviepicture,
                MovieDescription = pmoviedescription,
                MovieCategoryId = pmoviecategoryid,
                MovieFeature = pmoviefeature
            WHERE 
                MovieId = pmovieid;
            SELECT 'success' AS querystatus, '1' AS statusid;
         END IF;
    ELSE
    	IF EXISTS (SELECT MovieName FROM PM_Movies WHERE MovieName = pmoviename)
        THEN
        	SELECT 'Name already used by existing record' AS querystatus,'0' AS statusid;
        ELSE
          INSERT INTO PM_Movies(MovieName,MovieStatus,MoviePicture,MovieDescription,MovieCategoryId,MovieViews,MovieFeature)
          VALUES(pmoviename,pmoviestatus,pmoviepicture,pmoviedescription,pmoviecategoryid,0,0);
		SELECT LAST_INSERT_ID() AS pmovieid,'success' AS querystatus, '1' AS statusid;
         END IF;
    END IF;
END$$

CREATE DEFINER=`id9549775_denim1233`@`%` PROCEDURE `PM_Movie_AddView` (IN `pmovieid` INT)  MODIFIES SQL DATA
BEGIN
	UPDATE PM_Movies
    SET MovieViews = MovieViews + 1
    WHERE MovieId = pmovieid;
END$$

CREATE DEFINER=`id9549775_denim1233`@`%` PROCEDURE `PM_Movie_Count` (IN `pmovieid` INT(1))  READS SQL DATA
BEGIN
	UPDATE PM_Movies
    SET MovieStatus = 69
    WHERE MovieId = pmovieid;
END$$

CREATE DEFINER=`id9549775_denim1233`@`%` PROCEDURE `SYS_Category_Get` ()  READS SQL DATA
BEGIN
	SELECT * FROM SYS_Category
    ORDER BY CategoryId DESC;
END$$

CREATE DEFINER=`id9549775_denim1233`@`%` PROCEDURE `SYS_Category_Insert` (IN `pcategoryname` VARCHAR(100))  BEGIN

	INSERT INTO SYS_Category(CategoryName,CategoryStatus)
    VALUES(pcategoryname,1);

    SELECT LAST_INSERT_ID() AS pcategoryid;
END$$

CREATE DEFINER=`id9549775_denim1233`@`%` PROCEDURE `SYS_Category_ManageRecord` (IN `pcategoryid` INT, IN `pcategoryname` VARCHAR(100), IN `pcategorystatus` INT)  MODIFIES SQL DATA
BEGIN
    IF EXISTS (SELECT CategoryID from SYS_Category where CategoryId = pcategoryid)
    THEN
        IF EXISTS (SELECT CategoryName FROM SYS_Category WHERE CategoryName = pcategoryname AND CategoryId != pcategoryid)
        THEN
            SELECT 'Name already used by existing data' AS querystatus,'0' AS statusid;
        ELSE
          UPDATE 
            SYS_Category 
          SET 
            CategoryName = pcategoryname,
            CategoryStatus = pcategorystatus
          WHERE 
            CategoryId = pcategoryid;
          SELECT 'success' AS querystatus,'1' AS statusid;
        END IF;
    ELSE
          IF EXISTS (SELECT CategoryName FROM SYS_Category WHERE CategoryName = pcategoryname)
          THEN
                SELECT 'Name already used by existing data' AS querystatus,'0' AS statusid;
          ELSE
               INSERT INTO SYS_Category(CategoryName, CategoryStatus) 
              VALUES(pcategoryname,pcategorystatus);
                SELECT LAST_INSERT_ID() AS pcategoryid,'success' AS querystatus,'1' AS statusid;
          END IF;
    END IF;
END$$

CREATE DEFINER=`id9549775_denim1233`@`%` PROCEDURE `SYS_Category_Update` (IN `pCategoryId` INT, IN `pCategoryName` VARCHAR(100), IN `pCategoryStatus` INT)  READS SQL DATA
BEGIN
    IF EXISTS (SELECT CategoryID from SYS_Category where CategoryId = pcategoryid)
    Then
      UPDATE 
      	SYS_Category 
      SET 
      	CategoryName = pcategoryname,
        CategoryStatus = pcategorystatus
      WHERE 
      	CategoryId = pcategoryid;
    ELSE
      INSERT INTO SYS_Category(CategoryName, CategoryStatus) 
      VALUES(pcategoryname,pcategorystatus);
    END IF;
END$$

CREATE DEFINER=`id9549775_denim1233`@`%` PROCEDURE `SYS_Genre_Get` ()  READS SQL DATA
BEGIN
	SELECT * FROM SYS_Genre
    ORDER BY GenreId DESC;
END$$

CREATE DEFINER=`id9549775_denim1233`@`%` PROCEDURE `SYS_Genre_ManageRecord` (IN `pgenreid` INT, IN `pgenrename` VARCHAR(100), IN `pgenrestatus` INT, IN `pgenredescription` VARCHAR(500))  MODIFIES SQL DATA
BEGIN
    IF EXISTS (SELECT GenreId from SYS_Genre where GenreId = pgenreid)
    Then
    	IF EXISTS (SELECT GenreName FROM SYS_Genre WHERE GenreName = pgenrename AND GenreId != pgenreid)
        THEN
        	 SELECT 'Name already used by existing record' AS querystatus, '0' AS statusid;
        ELSE
              UPDATE 
                SYS_Genre
              SET
                GenreName = pgenrename,
                GenreStatus = pgenrestatus,
                GenreDescription = pgenredescription
              WHERE 
                GenreId = pgenreid;
              SELECT 'success' AS querystatus, '1' AS statusid;
       	END IF;
        	
    ELSE
    IF EXISTS (SELECT GenreName FROM SYS_Genre WHERE GenreName = pgenrename)
        THEN
        	 SELECT 'Name already used by existing record' AS querystatus, '0' AS statusid;
        ELSE
          INSERT INTO SYS_Genre(GenreName,GenreDescription,GenreStatus) 
          VALUES(pgenrename,pgenredescription,pgenrestatus);

          SELECT LAST_INSERT_ID() AS pgenreid,'success' AS querystatus,
          '1' AS statusid;
        END IF;
    END IF;
END$$

CREATE DEFINER=`id9549775_denim1233`@`%` PROCEDURE `SYS_User_Login` (IN `pusername` VARCHAR(100) CHARSET latin1, IN `puserpassword` VARCHAR(100) CHARSET latin1)  BEGIN
	SET @callbackmsg = '';
    SET @callbackstatusid = 0;
	IF EXISTS (SELECT * FROM SYS_User
    WHERE UserName = pusername COLLATE latin1_general_cs AND 
           UserPassword = puserpassword COLLATE latin1_general_cs)
    Then
		SET @callbackmsg = 'Status:Login Successful';
        SET @callbackstatusid = 1;
        SELECT @callbackmsg AS StatusMessage,
       			@callbackstatusid AS StatusId;
    ELSE
		SET @callbackmsg = 'Login Failed';
        SET @callbackstatusid = 0;
        SELECT @callbackmsg AS StatusMessage,
       			@callbackstatusid AS StatusId;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `PM_MovieGenre`
--

CREATE TABLE `PM_MovieGenre` (
  `MovieGenreId` int(11) NOT NULL,
  `MovieId` int(11) NOT NULL,
  `GenreId` int(11) NOT NULL,
  `MovieGenreStatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `PM_MovieGenre`
--

INSERT INTO `PM_MovieGenre` (`MovieGenreId`, `MovieId`, `GenreId`, `MovieGenreStatus`) VALUES
(22, 58, 16, 1),
(23, 58, 2, 1),
(24, 59, 2, 1),
(25, 60, 15, 1),
(26, 60, 14, 1),
(27, 61, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `PM_Movies`
--

CREATE TABLE `PM_Movies` (
  `MovieId` int(11) NOT NULL,
  `MovieName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `MovieStatus` int(11) NOT NULL,
  `MoviePicture` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `MovieDescription` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `MovieCategoryId` int(11) NOT NULL,
  `MovieViews` int(10) NOT NULL,
  `MovieFeature` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `PM_Movies`
--

INSERT INTO `PM_Movies` (`MovieId`, `MovieName`, `MovieStatus`, `MoviePicture`, `MovieDescription`, `MovieCategoryId`, `MovieViews`, `MovieFeature`) VALUES
(58, 'One Piece', 1, 'images/Onepiece.jpg', 'One Piece is a Japanese manga series written and illustrated by Eiichiro Oda. It has been serialized in Shueisha\'s Weekly Shōnen Jump magazine since July 22, 1997, and has been collected into 92 tankōbon volumes', 9, 6, 1),
(59, 'Naruto', 1, 'images/narutocover.jpg', 'Naruto is a Japanese manga series written and illustrated by Masashi Kishimoto. It tells the story of Naruto Uzumaki, a young ninja who searches for recognition from his peers and also dreams of becoming the Hokage, the leader of his village', 9, 1, 1),
(60, 'Bleach', 1, 'images/bleachcover.jpg', 'Ichigo Kurosaki never asked for the ability to see ghosts -- he was born with the gift. When his family is attacked by a Hollow -- a malevolent lost soul -- Ichigo becomes a Soul Reaper, dedicating his life to protecting the innocent and helping the tortured spirits themselves find peace', 9, 65, 1),
(61, 'One Punch Mans', 1, 'images/onepunchman.jpg', 'One-Punch Man is Japanese webcomic series created by ONE which began publication in early 2009. The series quickly went viral, surpassing 7.9 million hits in June 2012.', 9, 9, 1),
(62, 'Avengers: Endgame', 1, 'images/endgamecover.jpg', 'Adrift in space with no food or water, Tony Stark sends a message to Pepper Potts as his oxygen supply starts to dwindle. Meanwhile, the remaining Avengers -- Thor, Black Widow, Captain America and Bruce Banner -- must figure out a way to bring back their vanquished allies for an epic showdown with Thanos -- the evil demigod who decimated the planet and the universe.', 10, 22, 1),
(63, 'Godzilla: King of the Monsters', 1, 'images/godzilla.png', 'Members of the crypto-zoological agency Monarch face off against a battery of god-sized monsters, including the mighty Godzilla, who collides with Mothra, Rodan, and his ultimate nemesis, the three-headed King Ghidorah.', 10, 9, 1),
(64, 'Aladdin 2019', 69, 'images/alladin2019.png', 'Young Aladdin embarks on a magical adventure after finding a lamp that releases a wisecracking genie.', 10, 59, 1),
(65, 'John Wick 3: Parabellum', 1, 'images/johnwick.jpg', 'After gunning down a member of the High Table -- the shadowy international assassin\'s guild -- legendary hit man John Wick finds himself stripped of the organization\'s protective services', 10, 41, 1),
(66, 'Gundam', 1, 'images/gundam.jpg', 'Mobile Suit', 9, 0, 0),
(67, 'Boruto', 1, '', 'New', 9, 0, 0),
(68, 'Movie Tests', 1, '', 'New Data Test', 60, 0, 1),
(69, '1 liter of tears', 1, '', 'hard drama', 54, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `SYS_Category`
--

CREATE TABLE `SYS_Category` (
  `CategoryId` int(11) NOT NULL,
  `CategoryName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `CategoryStatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `SYS_Category`
--

INSERT INTO `SYS_Category` (`CategoryId`, `CategoryName`, `CategoryStatus`) VALUES
(9, 'Anime', 1),
(10, 'Movie', 1),
(11, 'TV Series', 1),
(12, 'Korean Drama', 1),
(53, 'Matured', 1),
(54, 'J Drama', 1),
(55, 'Fantasy', 1),
(56, 'Sci - Fi', 1),
(57, 'Trilogy', 1),
(58, 'Marvel', 1),
(59, 'DC', 1),
(60, 'Category 12', 1),
(61, 'Test Add', 1),
(62, 'Test Test', 1);

-- --------------------------------------------------------

--
-- Table structure for table `SYS_Genre`
--

CREATE TABLE `SYS_Genre` (
  `GenreId` int(11) NOT NULL,
  `GenreName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `GenreDescription` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `GenreStatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `SYS_Genre`
--

INSERT INTO `SYS_Genre` (`GenreId`, `GenreName`, `GenreDescription`, `GenreStatus`) VALUES
(1, 'Harems', ' is a kind of story in Japanese anime and manga where a male character is surrounded by many female characters', 1),
(2, 'Ecchi', 'comes from the sound of H in the word hentai, which in turn means pervert in Japanese', 1),
(3, 'Mystery', ' genre of fiction usually involving a mysterious death or a crime to be solved', 1),
(6, 'Horror', 'an overwhelming and painful feeling caused by something frightfully shocking, terrifying, or revolting; a shuddering fear: to shrink back from a mutilated corpse in horror. ', 1),
(13, 'Action', 'a66', 1),
(14, 'Sexy', 'New', 1),
(15, 'Melodrama', 'Smooth drama and emotionals', 1),
(16, 'Genre', 'Test Data', 1);

-- --------------------------------------------------------

--
-- Table structure for table `SYS_User`
--

CREATE TABLE `SYS_User` (
  `UserId` int(11) NOT NULL,
  `UserName` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `UserPassword` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `UserStatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `SYS_User`
--

INSERT INTO `SYS_User` (`UserId`, `UserName`, `UserPassword`, `UserStatus`) VALUES
(1, 'ApitAdmin', 'da5d30d4770db4bb129ec39797adc79dc59346d5ef3e5ab25a7c2be0c98bebf5', 1),
(2, 'Apitadmin2', 'b7e97ed07c43751b783d5326af678fc857b08554632515336dc9ad3ec292b676', 1),
(3, 'Apit', 'e7cf3ef4f17c3999a94f2c6f612e8a888e5b1026878e4e19398b23bd38ec221a', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `PM_MovieGenre`
--
ALTER TABLE `PM_MovieGenre`
  ADD PRIMARY KEY (`MovieGenreId`);

--
-- Indexes for table `PM_Movies`
--
ALTER TABLE `PM_Movies`
  ADD PRIMARY KEY (`MovieId`);

--
-- Indexes for table `SYS_Category`
--
ALTER TABLE `SYS_Category`
  ADD PRIMARY KEY (`CategoryId`);

--
-- Indexes for table `SYS_Genre`
--
ALTER TABLE `SYS_Genre`
  ADD PRIMARY KEY (`GenreId`);

--
-- Indexes for table `SYS_User`
--
ALTER TABLE `SYS_User`
  ADD PRIMARY KEY (`UserId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `PM_MovieGenre`
--
ALTER TABLE `PM_MovieGenre`
  MODIFY `MovieGenreId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `PM_Movies`
--
ALTER TABLE `PM_Movies`
  MODIFY `MovieId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `SYS_Category`
--
ALTER TABLE `SYS_Category`
  MODIFY `CategoryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `SYS_Genre`
--
ALTER TABLE `SYS_Genre`
  MODIFY `GenreId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `SYS_User`
--
ALTER TABLE `SYS_User`
  MODIFY `UserId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
