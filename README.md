# pixel finder

this project is being re-built, you can check what I'm working on on my [trello board](https://trello.com/b/vH66AXR5/pixelfinderhttps:/)

database files coming soon<br>

## Data used

official datasets: [2017](https://www.reddit.com/r/redditdata/comments/6640ru/place_datasets_april_fools_2017/) / [2022](https://www.reddit.com/r/place/comments/txvk2d/rplace_datasets_april_fools_2022/) / [2023](https://www.reddit.com/r/place/comments/15bjm5o/rplace_2023_data/)

scraped data:<br>
2017: [(data)](https://https://archive.org/details/place2017-opl) by [u/opl_](https://www.reddit.com/user/opl_)<br>
2022: [(data)](https://www.reddit.com/r/redditdata/comments/6640ru/place_datasets_april_fools_2017/) by [u/opl_](https://www.reddit.com/user/opl_)<br>
2023: [(data)](https://mod.ifies.com/f/230728_pixelhistory.xz) by [u/scaevolus](https://www.reddit.com/user/scaevolus) & [(data)](https://cdn.discordapp.com/attachments/297524632234229761/1133536680373133332/pixels.csv.zst) by [u/nepeat](https://www.reddit.com/u/nepeat/https:/)<br>

## How does it work

### finding hashes

the official dataset contains all data about pixels<br>
the thing is - you can't see who placed the pixel, there is only a hash that can't be reversed<br>
![](assets/20230805_202401_finding_hashes_1.png)

but by using scraped data, that was collected during the event - there is a chance to find a few of your pixels<br>
![](assets/20230805_202421_finding_hashes_2.png)

this data is not as accurate but by roughly comparing it to the official one, you get a list of possible hashes<br>
![](assets/20230805_202429_finding_hashes_3.png)

the only thing left to do now is to get a hash that occured the most times<br>
![](assets/20230805_202445_finding_hashes_4.png)

### trophies

endgame is by far the easiest to calculate, just check if the pixel was placed after the whiteout started<br>
![](assets/20230805_204445_trophy_1.png)

first placer and final canvas are a different story.<br>
this script: `/_data processing scripts_/process_trophies.py` goes through every pixel placement, and for every coordinate saves first and last users that have been there<br>
![](assets/20230805_205553_trophy_2.png)

using this we can connect trophies to the query for getting pixels<br>
![](assets/20230805_204502_trophy_3.png)<br>
this query also makes sure that only the correct pixel gets the trophy.<br>
for example: if there are multiple pixels placed on the same cords - only the first one has a chance to get a "first placer" trophy, and only the last one can get the "final canvas"<br><br>

```sql
SELECT date, color, dt.x, dt.y,
CASE WHEN tr.first_placer=hash AND ROW_NUMBER() OVER(PARTITION BY dt.x, dt.y ORDER BY date desc)=1 THEN TRUE ELSE FALSE END AS first_placer,
CASE WHEN tr.final_canvas=hash AND ROW_NUMBER() OVER(PARTITION BY dt.x, dt.y ORDER BY date)=1 THEN TRUE ELSE FALSE END AS final_canvas,
CASE WHEN date > '2022-04-04 22:47:40' THEN TRUE ELSE FALSE END AS endgame
FROM 2022_official dt
LEFT JOIN (
  SELECT * FROM 2022_trophy WHERE
	first_placer='Sn8jWuJQG2lTXoMRF1Ns9czd21CgXvPB1GZ4/j5LEYsUlX/RVsFLqyC1e2m1meTaQPilmhrUXkShfdlkuXo+UQ==' OR
	final_canvas='Sn8jWuJQG2lTXoMRF1Ns9czd21CgXvPB1GZ4/j5LEYsUlX/RVsFLqyC1e2m1meTaQPilmhrUXkShfdlkuXo+UQ=='
) as tr
ON tr.x=dt.x AND tr.y=dt.y
WHERE hash = 'Sn8jWuJQG2lTXoMRF1Ns9czd21CgXvPB1GZ4/j5LEYsUlX/RVsFLqyC1e2m1meTaQPilmhrUXkShfdlkuXo+UQ==';
```

*yeah, I know the hash is repeated 3 times, it's more efficient this way*<br>
source: just trust me bro<br>

## API

you can get raw data using this link:<br>
http://kisielo85.cba.pl/place/raw_result.php?nick=kisielo85&year=23<br>
