# pixel_finder-place2022

## how does it work

### finding hashes

the [official dataset](https://www.reddit.com/r/place/comments/txvk2d/rplace_datasets_april_fools_2022/) contains all the data about pixels

the thing is - you can't see who placed the pixel, there is only a hash that can't be reversed<br>
![image](https://user-images.githubusercontent.com/33911808/173188880-a7687db6-889e-4b9b-a886-d9d243220deb.png)

thankfully [u/opl_](https://www.reddit.com/user/opl_) was scraping the site during the event, so his [data](https://www.reddit.com/r/place/comments/txh660/dump_of_the_raw_unprocessed_data_i_collected/) contains usernames<br>
![image](https://user-images.githubusercontent.com/33911808/173189312-cbc0276d-ace7-436d-9135-ae9619773ade.png)

this data is not as accurate but by roughly comparing pixel cords and dates multiple times, you get a list of possible hashes<br>
![image](https://user-images.githubusercontent.com/33911808/173189766-b02d17ad-82fc-440d-b8df-e3ef56c434b1.png)

the only thing left to do now is to get the hash that occured the most times


### trophies
every pixel can get only one first_placer and one final_canvas trophy<br>
I made a script that checked all the pixels, and made a table showing who got trophies on every pixel<br>
![image](https://user-images.githubusercontent.com/33911808/173190055-456c892d-72d5-45e8-a071-5aec0e44c633.png)

if I want to check for example 69,420:<br>
69*2000+420+1 = 138421<br>
![image](https://user-images.githubusercontent.com/33911808/173190190-552777e0-f1bb-4336-9ef8-a4fd585c8bba.png)


