## API documentation

### `GET /bests_laps`

Get best laps for car category `car_cat` during given `period`

https://www.speed-dreams.net/masterserver/api/bests_laps?car_cat=Supercars&period=allTime

`period`: `today`, `week`, `month`, `year`, `allTime`

`page`: starting from 0 (0 by default)

`limit`: max amount of entries for requested page (0 by default)

Response:

```
{
  "data": [
    {
      "race_id": "10601",
      "track_id": "a-speedway",
      "car_id": "sc-cavallo-360",
      "user_id": "127",
      "timestamp": "2025-03-09 12:45:54",
      "wettness": "0",
      "bestlap": "32.004",
      "car_name": "Cavallo 360",
      "track_name": "A-Speedway",
      "username": "username_1"
    },
    {
      "race_id": "13448",
      "track_id": "alamo",
      "car_id": "sc-cavallo-360",
      "user_id": "1",
      "timestamp": "2025-05-09 19:40:20",
      "wettness": "0",
      "bestlap": "38.744",
      "car_name": "Cavallo 360",
      "track_name": "alamo",
      "username": "username_2"
    },
    ...
  ],
  "total": "50"
}
```

### `GET /most_active_users`

Get (up to) 20 most active users for car category `car_cat` during given `period`

https://www.speed-dreams.net/masterserver/api/most_active_users?car_cat=Supercars&period=allTime

Response:

```
{
  "data": [
    {
      "count": "500",
      "username": "username_1"
    },
    {
      "count": "200",
      "username": "username_2"
    },
    ...
  ],
  "total": "20"
}
```

### `GET /most_used_tracks`

Get most used tracks for car category `car_cat` during given `period`

Response:

```
{
  "data": [
    {
      "track_id": "torino",
      "count": "273",
      "track_name": "Torino"
    },
    {
      "track_id": "Ardennen-Spa",
      "count": "254",
      "track_name": "Ardennen Spa"
    },
    ...
  ],
  "total": "54"
}
```

### `GET /user_races`

Get races information for user `username`

https://www.speed-dreams.net/masterserver/api/user_races?username=torcs-ng

Response:

```
{
  "data": [
    {
      "id": "18919",
      "user_skill": "5",
      "track_id": "pinabashi",
      "car_id": "trb1-spirit-rb1lt",
      "startposition": "1",
      "endposition": "1",
      "sdversion": "v2.4.2-264-g252f80d2a-dirty",
      "timestamp": "2026-05-16 19:42:10",
      "type": "0",
      "track_name": "Pinabashi Park",
      "car_name": "Spirit RB1 LT"
    },
    {
      "id": "18918",
      "user_skill": "5",
      "track_id": "pinabashi",
      "car_id": "trb1-sector-rb1",
      "startposition": "1",
      "endposition": "1",
      "sdversion": "v2.4.2-264-g252f80d2a-dirty",
      "timestamp": "2026-05-16 19:39:12",
      "type": "0",
      "track_name": "Pinabashi Park",
      "car_name": "Sector RB-1"
    },
    ...
  ],
  "total": "70"
}
```

### `GET /championship_bestlaps`

Get races information for given championship id (race week number) `championship`

https://www.speed-dreams.net/masterserver/api/championship_bestlaps?championship=23


Response:

```
{
  "data": [
    {
      "race_id": "15625",
      "track_id": "alamo",
      "car_id": "mp1-diamond-r25",
      "user_id": "122",
      "timestamp": "2025-07-18 20:06:55",
      "wettness": "0",
      "laptime": "22.25",
      "car_name": "Diamond R25",
      "track_name": "alamo",
      "username": "username_1",
      "category_name": "Monoposto 1",
      "valid": "1"
    },
    {
      "race_id": "15606",
      "track_id": "alamo",
      "car_id": "mp1-diamond-r25",
      "user_id": "21",
      "timestamp": "2025-07-17 17:40:45",
      "wettness": "0",
      "laptime": "22.34",
      "car_name": "Diamond R25",
      "track_name": "alamo",
      "username": "username_2",
      "category_name": "Monoposto 1",
      "valid": "1"
    },
    {
      "race_id": "15626",
      "track_id": "alamo",
      "car_id": "mp1-vicente",
      "user_id": "141",
      "timestamp": "2025-07-18 20:06:58",
      "wettness": "0",
      "laptime": "29.416",
      "car_name": "MP1 Vicente",
      "track_name": "alamo",
      "username": "username_3",
      "category_name": "Monoposto 1",
      "valid": "1"
    }
  ]
}
```