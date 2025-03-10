package apiController

import (
	"net/http"

	"github.com/irumaru/iass/api/apiInterface"
	"github.com/irumaru/iass/api/db"
	"github.com/labstack/echo/v4"
)

func (a ApiController) CreateAnimeSeason(ctx echo.Context) error {
	animeSeasonB := apiInterface.AnimeSeason{}
	animeSeasonT := db.AnimeSeason{}

	conversionBind := func() {
		animeSeasonT.SeriesID = uint(animeSeasonB.SeriesId)
		animeSeasonT.Title = animeSeasonB.Title
		animeSeasonT.Synopsis = StringPtoV(animeSeasonB.Synopsis, "")
		animeSeasonT.Cours = StringPtoV(animeSeasonB.Cours, "")
		animeSeasonT.Cast = StringPtoV(animeSeasonB.Cast, "")
		animeSeasonT.Production = StringPtoV(animeSeasonB.Production, "")
	}

	return CreateGeneric(ctx, &animeSeasonB, &animeSeasonT, conversionBind)
}

func (a ApiController) GetAnimeSeasonList(ctx echo.Context, seriesId int) error {
	animeSeasonListT := []db.AnimeSeason{}
	animeSeasonListB := []apiInterface.AnimeSeason{}

	result := dbc.Where("series_id = ?", seriesId).Find(&animeSeasonListT)
	if result.Error != nil {
		return ctx.JSON(http.StatusInternalServerError, result.Error)
	}

	for _, AnimeSeasonT := range animeSeasonListT {
		animeSeasonB := apiInterface.AnimeSeason{
			Id:         int(AnimeSeasonT.ID),
			SeriesId:   int(AnimeSeasonT.SeriesID),
			Title:      AnimeSeasonT.Title,
			Synopsis:   &AnimeSeasonT.Synopsis,
			Cours:      &AnimeSeasonT.Cours,
			Cast:       &AnimeSeasonT.Cast,
			Production: &AnimeSeasonT.Production,
		}
		animeSeasonListB = append(animeSeasonListB, animeSeasonB)
	}

	return ctx.JSON(http.StatusOK, animeSeasonListB)
}

func (a ApiController) UpdateAnimeSeason(ctx echo.Context) error {
	animeSeasonB := apiInterface.AnimeSeason{}
	animeSeasonT := db.AnimeSeason{}

	conversionBind := func() {
		animeSeasonT.ID = uint(animeSeasonB.Id)
		animeSeasonT.SeriesID = uint(animeSeasonB.SeriesId)
		animeSeasonT.Title = animeSeasonB.Title
		animeSeasonT.Synopsis = StringPtoV(animeSeasonB.Synopsis, "")
		animeSeasonT.Cours = StringPtoV(animeSeasonB.Cours, "")
		animeSeasonT.Cast = StringPtoV(animeSeasonB.Cast, "")
		animeSeasonT.Production = StringPtoV(animeSeasonB.Production, "")
	}

	return UpdateGeneric(ctx, &animeSeasonB, &animeSeasonT, conversionBind)
}

func (a ApiController) DeleteAnimeSeason(ctx echo.Context, id int) error {
	animeSeasonT := db.AnimeSeason{}
	return DeleteGeneric(ctx, &animeSeasonT, id)
}
