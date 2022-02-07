DROP DATABASE IF EXISTS `forum_2022`;
CREATE DATABASE `forum_2022` DEFAULT CHAR
SET
utf8 COLLATE utf8_unicode_ci;

  USE forum_2022;
CREATE TABLE users(
    id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(40) NOT NULL,
    user_first_name VARCHAR(40) NOT NULL,
    INDEX user_idx_fullname (user_name,user_first_name)
  );
CREATE TABLE posts(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title varchar(80) NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    user_id MEDIUMINT UNSIGNED NOT NULL,
    parent_id INT UNSIGNED,
    INDEX idx_parent_id (parent_id),
    CONSTRAINT posts_to_author FOREIGN KEY (user_id) REFERENCES users (id),
    CONSTRAINT posts_to_parent FOREIGN KEY (parent_id) REFERENCES posts (id)
  );

  INSERT INTO users(user_first_name,user_name) VALUES("Bernard","Rene");

  CREATE INDEX post_idx_user_id ON posts (user_id);

  INSERT INTO posts(title,content,created_at,user_id,parent_id) VALUES
  ("Help me","JPP C BCP TROP",NOW(),1,null),
  ("TODO","MWA OSSI",NOW(),2,1);