# pixel finder

this project is being re-built, you can check what I'm working on on my [trello board](https://trello.com/b/vH66AXR5/pixelfinderhttps:/)

database files coming soon<br>

## Data used<br><br>

official datasets: [2017](https://www.reddit.com/r/redditdata/comments/6640ru/place_datasets_april_fools_2017/) / [2022](https://www.reddit.com/r/place/comments/txvk2d/rplace_datasets_april_fools_2022/) / [2023](https://www.reddit.com/r/place/comments/15bjm5o/rplace_2023_data/)

scraped data:
2017: [(data)](https://https://archive.org/details/place2017-opl) by [u/opl_](https://www.reddit.com/user/opl_)
2022: [(data)](https://www.reddit.com/r/redditdata/comments/6640ru/place_datasets_april_fools_2017/) by [u/opl_](https://www.reddit.com/user/opl_)
2023: [(data)](https://mod.ifies.com/f/230728_pixelhistory.xz) by [u/scaevolus](https://www.reddit.com/user/scaevolus) & [(data)](https://cdn.discordapp.com/attachments/297524632234229761/1133536680373133332/pixels.csv.zst) by [u/nepeat](https://www.reddit.com/u/nepeat/https:/)

## How does it work

### finding hashes

the official dataset contains all data about pixels<br>
the thing is - you can't see who placed the pixel, there is only a hash that can't be reversed<br>
(image)

but by using scraped data, that was collected during the event - there is a chance to find a few of your pixels<br>
(image)

this data is not as accurate but by roughly comparing it to the official one, you get a list of possible hashes<br>
(image)

<br>
the only thing left to do now is to get a hash that occured the most times
<br><br>

### trophies

endgame is by far the easiest to calculate, just check if the pixel was placed after the whiteout started
(image)

first placer and final canvas are a different story.
this script: `/_data processing scripts_/process_trophies.py` goes through every pixel placement, and for every coordinate saves first and last users that have been there
(image)

now we can connect add trophies to the query for getting pixels
(image)

one more thing worth noting is this case:
(image)
if someone placed multiple pixels in the same X Y, all of them get the trophy

to fix this wee need to check if a pixel is the first one on these cords, co It could qualify for first placer trophy
same goes for final canvas, but in reverse, a pixel has to be the last one
(image)

so here is the final sql query:

```sql
SELECT date, color, data23.x, data23.y,
CASE WHEN tr.first_placer=hash AND ROW_NUMBER() OVER(PARTITION BY data23.x, data23.y ORDER BY date desc)=1 THEN TRUE ELSE FALSE END AS first_placer,
CASE WHEN tr.final_canvas=hash AND ROW_NUMBER() OVER(PARTITION BY data23.x, data23.y ORDER BY date)=1 THEN TRUE ELSE FALSE END AS final_canvas,
CASE WHEN date > '2023-07-25 19:44:00' THEN TRUE ELSE FALSE END AS endgame
FROM data23
LEFT JOIN (
  SELECT * FROM 2023_trophy WHERE
	first_placer='srHuiEQEJUaUgMM/fJP6H3rJEQzedguA+qX1J87T78l6SlsTKcI1Wo1hvutir8U0YKFVuEUrNRLSOVLD4c93Zg==' OR
	final_canvas='srHuiEQEJUaUgMM/fJP6H3rJEQzedguA+qX1J87T78l6SlsTKcI1Wo1hvutir8U0YKFVuEUrNRLSOVLD4c93Zg=='
) as tr
ON CONCAT('"',tr.x)=data23.x AND CONCAT(tr.y,'"')=data23.y
WHERE hash = 'srHuiEQEJUaUgMM/fJP6H3rJEQzedguA+qX1J87T78l6SlsTKcI1Wo1hvutir8U0YKFVuEUrNRLSOVLD4c93Zg==';
```

*yeah, I know the hash is repeated 3 times, it's more efficient this way*
source: just trust me bro

## API

you can get raw data using this link:<br>
http://kisielo85.cba.pl/place2022/raw_result.php?nick=kisielo85?year=23<br>
