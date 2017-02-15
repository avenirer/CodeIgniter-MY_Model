-- phpMyAdmin SQL Dump
-- version 4.6.5.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 15, 2017 at 09:10 AM
-- Server version: 5.6.34
-- PHP Version: 7.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mymodel`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `deleted_at`, `deleted_by`, `updated_at`, `updated_by`, `created_at`, `created_by`) VALUES
(1, 'First article title', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Another article title', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'One more article title', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'This article has a title too', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'How about this article title', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'Test to be deleted', NULL, NULL, NULL, NULL, '2017-02-15 08:18:14', NULL),
(7, 'Test to be deleted', NULL, NULL, NULL, NULL, '2017-02-15 08:19:19', NULL),
(8, 'Test to be deleted', '2017-02-15 08:20:39', NULL, NULL, NULL, '2017-02-15 08:20:39', NULL),
(9, 'Test to be deleted', '2017-02-15 08:26:44', NULL, NULL, NULL, '2017-02-15 08:26:44', NULL),
(10, 'Test to be deleted', '2017-02-15 08:28:33', NULL, NULL, NULL, '2017-02-15 08:28:33', NULL),
(11, 'Test to be deleted', '2017-02-15 08:28:37', NULL, NULL, NULL, '2017-02-15 08:28:37', NULL),
(12, 'Test to be deleted', '2017-02-15 08:30:10', NULL, NULL, NULL, '2017-02-15 08:30:10', NULL),
(13, 'Test to be deleted', '2017-02-15 08:31:22', NULL, NULL, NULL, '2017-02-15 08:31:22', NULL),
(14, 'Test to be deleted', '2017-02-15 09:01:01', NULL, NULL, NULL, '2017-02-15 09:01:01', NULL),
(15, 'Test to be deleted', '2017-02-15 09:01:22', NULL, NULL, NULL, '2017-02-15 09:01:22', NULL),
(16, 'Test to be deleted', '2017-02-15 09:02:42', NULL, NULL, NULL, '2017-02-15 09:02:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `articles_users`
--

CREATE TABLE `articles_users` (
  `article_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `articles_users`
--

INSERT INTO `articles_users` (`article_id`, `user_id`) VALUES
(1, 1),
(2, 1),
(2, 2),
(3, 2),
(3, 5),
(4, 3),
(5, 3),
(5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `version` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`version`) VALUES
(20150924133253);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `content`) VALUES
(1, 1, 'First title', 'This is content for first title'),
(2, 3, 'Another title', 'This is content for another title'),
(3, 3, 'One more title', 'This is content for one more title'),
(4, 5, 'This one has a title too', 'This is content for this title too'),
(5, 5, 'How about this title', 'This is content for how about this title');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, 'user1', 'user1@user.com', 'mypass', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'user2', 'user2@user.com', 'nopass', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'avenirer', 'user3@user.com', 'nopass', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'administrator', 'user4@user.com', 'mypass', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'user5', 'user5@user.com', 'nopass', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`user_id`, `first_name`, `last_name`, `address`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, 'John', 'Doe', 'no stable address', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Jane', 'Doe', 'same as John Doe\'s', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Adrian', 'Voicu', 'Bucharest, Romania', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Admin', 'Istrator', 'over us', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Whoever', 'You want', 'Wherever', NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
