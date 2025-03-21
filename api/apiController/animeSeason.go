package apiController

import (
	"net/http"

	"github.com/irumaru/iass/api/apiInterface"
	"github.com/irumaru/iass/api/db"
	"github.com/labstack/echo/v4"
)

func arrayToCastIds(CastsId *[]int) []db.Cast {
	CastIdsT := []db.Cast{}
	for _, CastId := range *CastsId {
		CastIdsT = append(CastIdsT, db.Cast{ID: uint(CastId)})
	}
	return CastIdsT
}

func castIdsToArray(CastIdsT []db.Cast) *[]int {
	CastsIds := []int{}
	for _, Cast := range CastIdsT {
		CastsIds = append(CastsIds, int(Cast.ID))
	}
	return &CastsIds
}

func (a ApiController) CreateAnimeSeason(ctx echo.Context) error {
	animeSeasonB := apiInterface.AnimeSeason{}
	animeSeasonT := db.AnimeSeason{}

	conversionBind := func() {
		animeSeasonT.SeriesID = uint(animeSeasonB.SeriesId)
		animeSeasonT.DisplayNumber = uint(IntPtoV(animeSeasonB.DisplayNumber, 0))
		animeSeasonT.Title = animeSeasonB.Title
		animeSeasonT.Synopsis = StringPtoV(animeSeasonB.Synopsis, "")
		animeSeasonT.Cours = StringPtoV(animeSeasonB.Cours, "")
		animeSeasonT.Cast = arrayToCastIds(animeSeasonB.CastsId)
		animeSeasonT.Production = StringPtoV(animeSeasonB.Production, "")
	}

	return CreateGeneric(ctx, &animeSeasonB, &animeSeasonT, conversionBind)
}

func (a ApiController) GetAnimeSeasonList(ctx echo.Context, seriesId int) error {
	animeSeasonListT := []db.AnimeSeason{}
	animeSeasonListB := []apiInterface.AnimeSeason{}

	result := dbc.Preload("Cast").Where("series_id = ?", seriesId).Find(&animeSeasonListT)
	if result.Error != nil {
		return ctx.JSON(http.StatusInternalServerError, result.Error)
	}

	for _, AnimeSeasonT := range animeSeasonListT {
		animeSeasonB := apiInterface.AnimeSeason{
			Id:            int(AnimeSeasonT.ID),
			SeriesId:      int(AnimeSeasonT.SeriesID),
			DisplayNumber: IntVtoP(int(AnimeSeasonT.DisplayNumber)),
			Title:         AnimeSeasonT.Title,
			Synopsis:      &AnimeSeasonT.Synopsis,
			Cours:         &AnimeSeasonT.Cours,
			CastsId:       castIdsToArray(AnimeSeasonT.Cast),
			Production:    &AnimeSeasonT.Production,
		}
		animeSeasonListB = append(animeSeasonListB, animeSeasonB)
	}

	return ctx.JSON(http.StatusOK, animeSeasonListB)
}

func (a ApiController) GetAnimeSeason(ctx echo.Context, id int) error {
	animeSeasonT := db.AnimeSeason{}
	animeSeasonB := apiInterface.AnimeSeason{}

	conversionBind := func() {
		animeSeasonB.Id = int(animeSeasonT.ID)
		animeSeasonB.SeriesId = int(animeSeasonT.SeriesID)
		animeSeasonB.DisplayNumber = IntVtoP(int(animeSeasonT.DisplayNumber))
		animeSeasonB.Title = animeSeasonT.Title
		animeSeasonB.Synopsis = &animeSeasonT.Synopsis
		animeSeasonB.Cours = &animeSeasonT.Cours
		animeSeasonB.CastsId = castIdsToArray(animeSeasonT.Cast)
		animeSeasonB.Production = &animeSeasonT.Production
	}

	return GetGenericPreload(ctx, &animeSeasonB, &animeSeasonT, conversionBind, id, "Cast")
}

func (a ApiController) UpdateAnimeSeason(ctx echo.Context) error {
	animeSeasonB := apiInterface.AnimeSeason{}
	animeSeasonT := db.AnimeSeason{}

	conversionBind := func() {
		animeSeasonT.ID = uint(animeSeasonB.Id)
		animeSeasonT.SeriesID = uint(animeSeasonB.SeriesId)
		animeSeasonT.DisplayNumber = uint(IntPtoV(animeSeasonB.DisplayNumber, 0))
		animeSeasonT.Title = animeSeasonB.Title
		animeSeasonT.Synopsis = StringPtoV(animeSeasonB.Synopsis, "")
		animeSeasonT.Cours = StringPtoV(animeSeasonB.Cours, "")
		animeSeasonT.Cast = arrayToCastIds(animeSeasonB.CastsId)
		animeSeasonT.Production = StringPtoV(animeSeasonB.Production, "")
	}

	return UpdateAssociationGeneric(ctx, &animeSeasonB, &animeSeasonT, &animeSeasonT.Cast, "Cast", conversionBind)
}

func (a ApiController) DeleteAnimeSeason(ctx echo.Context, id int) error {
	animeSeasonT := db.AnimeSeason{}
	return DeleteGeneric(ctx, &animeSeasonT, id)
}
