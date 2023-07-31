# pixel_finder-place2022

this project is being re-built

updated readme and database files coming soon<br>

<br><br>
Thanks to [u/opl_](https://www.reddit.com/user/opl_) for sharing his [scraped data](https://www.reddit.com/r/place/comments/txh660/dump_of_the_raw_unprocessed_data_i_collected/) that made the project possible<br>
also, check out [ShadowLp174](https://github.com/ShadowLp174) and his [discord bot](https://github.com/PRRQRC/pixel-finder-bot) for finding pixels *(in development)*
<br>

## how does it work

### finding hashes

the [official dataset](https://www.reddit.com/r/place/comments/txvk2d/rplace_datasets_april_fools_2022/) contains all data about pixels<br>
the thing is - you can't see who placed the pixel, there is only a hash that can't be reversed<br>
![image](https://user-images.githubusercontent.com/33911808/173188880-a7687db6-889e-4b9b-a886-d9d243220deb.png)

luckily, [u/opl_](https://www.reddit.com/user/opl_) was scraping the site during the event, so his [data](https://www.reddit.com/r/place/comments/txh660/dump_of_the_raw_unprocessed_data_i_collected/) contains usernames<br>
![image](https://user-images.githubusercontent.com/33911808/173189312-cbc0276d-ace7-436d-9135-ae9619773ade.png)

this data is not as accurate but by roughly comparing pixel cords and dates multiple times, you get a list of possible hashes<br>
![image](https://user-images.githubusercontent.com/33911808/173189766-b02d17ad-82fc-440d-b8df-e3ef56c434b1.png)<br>
the only thing left to do now is to get a hash that occured the most times
<br><br>

<br>

## API

you can get raw data using this link:<br>
http://kisielo85.cba.pl/place2022/raw_result.php?nick=kisielo85?year=23<br>
