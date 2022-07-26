drop database if exists greendit;

create database if not exists greendit;

use greendit;

create table if not exists users (
    user_id int not null auto_increment,
    email varchar(45) null default null,
    username varchar(45) not null,
    password varchar(256) not null,
    created_at datetime not null default current_timestamp,
    primary key (user_id)
);

create table if not exists communities (
    community_id int not null auto_increment,
    name varchar(45) not null,
    shortname varchar(24) not null,
    user_id int not null,
    created_at datetime not null default current_timestamp,
    primary key (community_id),
    index fk_user_idx (user_id asc) visible,
    constraint fk_user_id foreign key (user_id) references users (user_id)
);

create table if not exists posts (
    post_id int not null auto_increment,
    title varchar(45) not null,
    content text,
    hash varchar(6) not null,
    user_id int not null,
    community_id int not null,
    status enum('public','draft','removed') not null,
    created_at datetime not null default current_timestamp,
    primary key (post_id),
    index fk_posts_users (user_id asc) visible,
    index fk_community1_idx (community_id asc) visible,
    constraint fk_posts_users foreign key (user_id) references users (user_id),
    constraint fk_community1 foreign key (community_id) references communities (community_id) on delete cascade
);

create table if not exists comments (
    comment_id int not null auto_increment,
    content varchar(250) not null,
    hash varchar(6) not null,
    post_id int not null,
    user_id int not null,
    parent_id int null,
    created_at datetime not null default current_timestamp,
    primary key (comment_id),
    index fk_comments_posts_idx (post_id asc) visible,
    index fk_comments_users (user_id asc) visible,
    index fk_comments3_idx (parent_id asc) visible,
    constraint fk_comments_posts foreign key (post_id) references posts (post_id),
    constraint fk_comments_users foreign key (user_id) references users (user_id),
    constraint fk_comments3 foreign key (parent_id) references comments (comment_id) on delete cascade
);

create table if not exists comment_likes (
    like_id int not null auto_increment,
    user_id int not null,
    comment_id int not null,
    primary key (like_id),
    index fk_users1_idx (user_id asc) visible,
    index fk_comments1_idx (comment_id asc) visible,
    constraint fk_comments1 foreign key (comment_id) references comments (comment_id),
    constraint fk_users1 foreign key (user_id) references users (user_id)
);

create table if not exists post_likes (
    like_id int not null auto_increment,
    user_id int not null,
    post_id int not null,
    primary key (like_id),
    index fk_likes_posts1_idx (post_id asc) visible,
    index fk_likes_users1_idx (user_id asc) visible,
    constraint fk_likes_posts1 foreign key (post_id) references posts (post_id) on delete cascade,
    constraint fk_likes_users1 foreign key (user_id) references users (user_id) on delete cascade
);

create table if not exists post_dislikes (
    dislike_id int not null auto_increment,
    user_id int not null,
    post_id int not null,
    primary key (dislike_id),
    index fk_users2_idx (user_id asc) visible,
    index fk_posts1_idx (post_id asc) visible,
    constraint fk_users2 foreign key (user_id) references users (user_id) on delete cascade,
    constraint fk_posts1 foreign key (post_id) references posts (post_id) on delete cascade
);

create table if not exists comment_dislikes (
    dislike_id int not null auto_increment,
    user_id int not null,
    comment_id int not null,
    primary key (dislike_id),
    index fk_comments2_idx (comment_id asc) visible,
    index fk_users3_idx (user_id asc) visible,
    constraint fk_comments2 foreign key (comment_id) references comments (comment_id) on delete cascade,
    constraint fk_users3 foreign key (user_id) references users (user_id) on delete cascade
);

create table if not exists saved_posts (
    save_id int not null auto_increment,
    user_id int not null,
    post_id int not null,
    primary key (save_id),
    index fk_users4_idx (user_id asc) visible,
    index fk_posts2_idx (post_id asc) visible,
    constraint fk_users4 foreign key (user_id) references users (user_id) on delete cascade,
    constraint fk_posts2 foreign key (post_id) references posts (post_id) on delete cascade
);

create table if not exists saved_comments (
    save_id int not null auto_increment,
    user_id int not null,
    comment_id int not null,
    primary key (save_id),
    index fk_users5_idx (user_id asc) visible,
    index fk_comments4_idx (comment_id asc) visible,
    constraint fk_users5 foreign key (user_id) references users (user_id) on delete cascade,
    constraint fk_comments4 foreign key (comment_id) references comments (comment_id) on delete cascade
);

create table if not exists followers (
    link_id varchar(45) not null,
    user_id int not null,
    follower_id int not null,
    primary key (link_id),
    index fk_users6_idx (user_id asc) visible,
    index fk_users7_idx (follower_id asc) visible,
    constraint fk_users6 foreign key (user_id) references users (user_id) on delete cascade,
    constraint fk_users7 foreign key (follower_id) references users (user_id) on delete cascade
);

create table if not exists joined_communities (
    link_id int not null,
    user_id int not null,
    community_id int not null,
    primary key (link_id),
    index fk_users8_idx (user_id asc) visible,
    index fk_communities1_idx (community_id asc) visible,
    constraint fk_users8 foreign key (user_id) references users (user_id) on delete cascade,
    constraint fk_communities1 foreign key (community_id) references communities (community_id) on delete cascade
);

create table if not exists post_media (
    media_id int not null auto_increment,
    post_id int not null,
    file_name varchar(40) not null,
    primary key (media_id),
    index fk_post_idx (post_id asc) visible,
    constraint fk_post foreign key (post_id) references posts (post_id) on delete cascade
);