drop database if exists greendit;

create database if not exists greendit;

use greendit;

create table if not exists users (
  user_id int not null auto_increment,
  email varchar(45) null,
  username varchar(45) not null,
  password varchar(256) not null,
  created_at datetime not null default current_timestamp,
  primary key (user_id)
);

create table if not exists posts (
  post_id int not null auto_increment,
  title varchar(45) not null,
  content text null,
  created_at datetime not null default current_timestamp,
  user_id int not null,
  primary key (post_id),
  constraint fk_posts_users foreign key (user_id) references users (user_id)
);

create table if not exists post_likes (
  like_id int not null auto_increment,
  post_id int not null,
  user_id int not null,
  primary key (like_id),
  index fk_likes_posts1_idx (post_id asc) visible,
  index fk_likes_users1_idx (user_id asc) visible,
  constraint fk_likes_posts1 foreign key (post_id) references posts (post_id) on delete cascade,
  constraint fk_likes_users1 foreign key (user_id) references users (user_id) on delete cascade
);

create table if not exists comments (
  comment_id int not null auto_increment,
  content varchar(250) null,
  post_id int not null,
  user_id int not null,
  primary key (comment_id),
  index fk_comments_posts_idx (post_id asc) visible,
  constraint fk_comments_posts foreign key (post_id) references posts (post_id),
  constraint fk_comments_users foreign key (user_id) references users (user_id)
);

create table if not exists comment_likes (
  like_id int not null,
  user_id int not null,
  comment_id int not null,
  primary key (like_id),
  index fk_users1_idx (user_id asc) visible,
  index fk_comments1_idx (comment_id asc) visible,
  constraint fk_users1 foreign key (user_id) references users (user_id),
  constraint fk_comments1 foreign key (comment_id) references comments (comment_id)
);