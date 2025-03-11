package apiController

import (
	"net/http"

	"github.com/irumaru/iass/api/apiInterface"
	"github.com/irumaru/iass/api/db"
	"github.com/labstack/echo/v4"
	"gorm.io/gorm"
)

func (a ApiController) CreateSeries(ctx echo.Context) error {
	seriesB := apiInterface.Series{}
	seriesT := db.Series{}

	conversionBind := func() {
		seriesT.Title = seriesB.Title
	}

	return CreateGeneric(ctx, &seriesB, &seriesT, conversionBind)
}

func (a ApiController) GetSeriesList(ctx echo.Context, params apiInterface.GetSeriesListParams) error {
	q := params.Q

	seriesListT := []db.Series{}
	seriesListB := []apiInterface.Series{}

	var result *gorm.DB
	if q == nil {
		result = dbc.Find(&seriesListT)
	} else {
		result = dbc.Where("title LIKE %?%", *q).Find(&seriesListT)
	}
	if result.Error != nil {
		return ctx.JSON(http.StatusInternalServerError, result.Error)
	}

	for _, seriesT := range seriesListT {
		seriesB := apiInterface.Series{
			Id:    int(seriesT.ID),
			Title: seriesT.Title,
		}
		seriesListB = append(seriesListB, seriesB)
	}

	return ctx.JSON(http.StatusOK, seriesListB)
}

func (a ApiController) UpdateSeries(ctx echo.Context) error {
	seriesB := apiInterface.Series{}
	seriesT := db.Series{}

	conversionBind := func() {
		seriesT.ID = uint(seriesB.Id)
		seriesT.Title = seriesB.Title
	}

	return UpdateGeneric(ctx, &seriesB, &seriesT, conversionBind)
}

func (a ApiController) DeleteSeries(ctx echo.Context, id int) error {
	seriesT := db.Series{}
	return DeleteGeneric(ctx, &seriesT, id)
}
