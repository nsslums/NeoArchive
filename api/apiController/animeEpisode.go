package apiController

import (
	"net/http"

	"github.com/irumaru/iass/api/apiInterface"
	"github.com/irumaru/iass/api/db"
	"github.com/labstack/echo/v4"
)

func (a ApiController) CreateAnimeEpisode(ctx echo.Context) error {
	animeEpisodeB := apiInterface.AnimeEpisode{}
	animeEpisodeT := db.AnimeEpisode{}

	conversionBind := func() {
		animeEpisodeT.AnimeSeasonID = uint(animeEpisodeB.SeasonId)
		animeEpisodeT.VideoID = uint(animeEpisodeB.VideoId)
		animeEpisodeT.Subtitle = StringPtoV(animeEpisodeB.Subtitle, "")
		animeEpisodeT.Number = StringPtoV(animeEpisodeB.Number, "")
	}

	return CreateGeneric(ctx, &animeEpisodeB, &animeEpisodeT, conversionBind)
}

func (a ApiController) GetAnimeEpisodeList(ctx echo.Context, seasonId int) error {
	animeEpisodeListT := []db.AnimeEpisode{}
	animeEpisodeListB := []apiInterface.AnimeEpisode{}

	result := dbc.Where("anime_season_id = ?", seasonId).Find(&animeEpisodeListT)
	if result.Error != nil {
		return ctx.JSON(http.StatusInternalServerError, result.Error)
	}

	for _, AnimeEpisodeT := range animeEpisodeListT {
		animeEpisodeB := apiInterface.AnimeEpisode{
			Id:       int(AnimeEpisodeT.ID),
			SeasonId: int(AnimeEpisodeT.AnimeSeasonID),
			VideoId:  int(AnimeEpisodeT.VideoID),
			Subtitle: &AnimeEpisodeT.Subtitle,
			Number:   &AnimeEpisodeT.Number,
		}
		animeEpisodeListB = append(animeEpisodeListB, animeEpisodeB)
	}

	return ctx.JSON(http.StatusOK, animeEpisodeListB)
}

func (a ApiController) UpdateAnimeEpisode(ctx echo.Context) error {
	animeEpisodeB := apiInterface.AnimeEpisode{}
	animeEpisodeT := db.AnimeEpisode{}

	conversionBind := func() {
		animeEpisodeT.ID = uint(animeEpisodeB.Id)
		animeEpisodeT.AnimeSeasonID = uint(animeEpisodeB.SeasonId)
		animeEpisodeT.VideoID = uint(animeEpisodeB.VideoId)
		animeEpisodeT.Subtitle = StringPtoV(animeEpisodeB.Subtitle, "")
		animeEpisodeT.Number = StringPtoV(animeEpisodeB.Number, "")
	}

	return UpdateGeneric(ctx, &animeEpisodeB, &animeEpisodeT, conversionBind)
}

func (a ApiController) DeleteAnimeEpisode(ctx echo.Context, id int) error {
	animeEpisodeT := db.AnimeEpisode{}
	return DeleteGeneric(ctx, &animeEpisodeT, id)
}
