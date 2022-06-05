use greendit;

insert into
    users(username, password)
values
    ('admin', '$argon2id$v=19$m=65536,t=4,p=1$ajh0Y0llRXh6Vjk3TDFjSw$mhSvEtFT0HkvrAWyFmxU9zIYFl+txxzDAwj0Rk02c1M'),
    ('user1','$argon2id$v=19$m=65536,t=4,p=1$cHRlcFdFOVpEeFNLU1RWQg$gRdZ3DSCS9OCIFeh9wcu7bKXyzsI9lYsnvfitG3iqUc'),
    ('user2', '$argon2id$v=19$m=65536,t=4,p=1$ajh0Y0llRXh6Vjk3TDFjSw$mhSvEtFT0HkvrAWyFmxU9zIYFl+txxzDAwj0Rk02c1M');

insert into
    posts(title, content, user_id)
values
    ('First post', 'This is the first post.', (select user_id from users where username = 'user1')),
    ('Second post', 'This is the second post.', (select user_id from users where username = 'user1')),
    ('Third post', 'This is the third post.', (select user_id from users where username = 'user2'));