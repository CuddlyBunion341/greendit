use greendit;

insert into
    users(username, password)
values
    (
        'admin',
        '$2y$10$tPEyNHgaerznOhUW8e0vfefXrLva24qMue.X5eHeptwEM0r81SPTu'
        -- password: admin
    ),
    (
        'cb341',
        '$2y$10$zl6iDqfSRyY0xuOY11wqjuH68/GBi71nf1LqhqzEWCZVhrUFfRZti'
        -- password: password
    ),
    (
        'JeJe69',
        '$2y$10$sscT5dQO17mmP1zWgIQoT.FJh.DPa9c6pJoz78thWJYTX7DYKRNBq'
        -- password: password
    ),
    (
        'ghwalin',
        '$2y$10$gvE.Jxpx5igxQptthxY5SuGUqwpvokdUgazu1Cq/fZ96xEw.1QBOu'
        -- password: test
    );

insert into
    communities(name, shortname, user_id)
values
    ('main sub', 'main', 1),
    ('funny meme sub', 'meme', 2),
    ('memes about programing', 'programmerHumor', 4),
    ('the real help sub', 'itSupport', 3),
    ('pro gamer gaming sub','gaming', 2);

insert into
    posts(title, content, user_id, community_id)
values
    ('First post', 'This is the first post!', 1, 1),
    ('Second post', 'Who could have expected this?',1,1),
    ('Need some help!','My code wont run!',4,4);

insert into
    post_likes(user_id,post_id)
values
    (1,1),
    (1,2),
    (2,1),
    (3,2),
    (4,3);