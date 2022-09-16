# User: 
## URL Browse: /api/v1/users {GET}
## URL Read: /api/v1/users/id {GET}
## URL Edit: /api/v1/users/id {PATCH}
## URL Add: /api/v1/users/id {POST}
## URL Delete: /api/v1/users/id {DELETE}

```json
[
  {
    "id": 1,
    "email": "tux@keskonmate.io",
    "username": "tux@keskonmate.io",
    "createdAt": "2021-10-27T12:09:27+02:00",
    "updatedAt": null,
    "userlist": [
      {
        "id": 1,
        "seasonNb": null,
        "seriesNb": 1,
        "episodeNb": null,
        "createdAt": "2021-10-27T12:40:34+02:00",
        "updatedAt": null,
        "type": 1,
        "series": [
          {
            "id": 1,
            "title": "GoT",
            "Synopsis": "gfvdgfdfgdfgds",
            "releaseDate": null,
            "image": "dfgfdg.jpg",
            "director": "",
            "numberOfSeasons": 5,
            "createdAt": "2021-10-27T15:18:38+02:00",
            "updatedAt": null,
            "genre": [
              {
                "id": 1,
                "name": "Action"
              }
            ],
            "season": [
              {
                "id": 2,
                "seasonNumber": null
              }
            ]
          }
        ]
      },      
    ]
  },
  ```

# Actors:
## URL Browse: /api/v1/actors
## URL Read: /api/v1/actors/id

```json
[
  {
    "id": 1,
    "firstname": "Keanu",
    "lastname": "Reeves",
    "image": "kreeves.jpg",
    "createdAt": "2021-10-27T12:10:49+02:00",
    "updatedAt": null,
    "series": [
      {
        "id": 1,
        "title": "GoT"
      }
    ]
  }
]
```

# Genres:
## URL Browse: /api/v1/genres
## URL Read: /api/v1/genres/id

```json
[
  {
    "id": 1,
    "name": "Action",
    "createdAt": "2021-10-27T12:34:40+02:00",
    "updatedAt": null,
    "series": [
      {
        "id": 1,
        "title": "GoT"
      }
    ]
  }
]
```

# Season:
## URL Browse: /api/v1/seasons
## URL Read: /api/v1/seasons/id

```json
[
  {
    "id": 2,
    "seasonNumber": null,
    "numberOfEpisodes": null,
    "createdAt": "2021-10-27T12:38:45+02:00",
    "updatedAt": null,
    "series": {
      "id": 1,
      "title": "GoT"
    }
  }
]
```

# Series:
## URL Browse: /api/v1/series
## URL Read: /api/v1/series/id


```json
[
  {
    "id": 1,
    "title": "GoT",
    "Synopsis": "gfvdgfdfgdfgds",
    "releaseDate": null,
    "image": "dfgfdg.jpg",
    "director": "",
    "numberOfSeasons": 5,
    "createdAt": "2021-10-27T15:18:38+02:00",
    "updatedAt": null,
    "genre": [
      {
        "id": 1,
        "name": "Action"
      }
    ],
    "season": [
      {
        "id": 2,
        "seasonNumber": null
      }
    ],
    "actor": [
      {
        "id": 1,
        "firstname": "Keanu",
        "lastname": "Reeves",
        "image": "kreeves.jpg"
      }
    ]
  }
]
```

# Userlist: 
## URL Browse: /api/v1/userlists {GET}
## URL Read: /api/v1/userlists/id {GET}
## URL Edit: /api/v1/userlists/id {PATCH}
## URL Add: /api/v1/userlists {POST}

```json
[
  {
    "id": 1,
    "seasonNb": null,
    "seriesNb": 1,
    "episodeNb": null,
    "createdAt": "2021-10-27T12:40:34+02:00",
    "updatedAt": null,
    "type": 1,
    "series": [
      {
        "id": 1,
        "title": "GoT"
      }
    ],
    "users": {
      "id": 1,
      "email": "tux@keskonmate.io"
    }
  },
  {
    "id": 2,
    "seasonNb": 1,
    "seriesNb": 1,
    "episodeNb": 2,
    "createdAt": "2021-10-27T15:31:06+02:00",
    "updatedAt": null,
    "type": 2,
    "series": [],
    "users": {
      "id": 1,
      "email": "tux@keskonmate.io"
    }
  }
]
```