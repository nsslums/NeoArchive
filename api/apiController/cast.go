package apiController

import (
	"net/http"

	"github.com/irumaru/iass/api/apiInterface"
	"github.com/irumaru/iass/api/db"
	"github.com/labstack/echo/v4"
)

func (a ApiController) CreateCast(ctx echo.Context) error {
	castB := apiInterface.Cast{}
	castT := db.Cast{}

	conversionBind := func() {
		castT.Name = castB.Name
	}

	return CreateGeneric(ctx, &castB, &castT, conversionBind)
}

func (a ApiController) GetCastList(ctx echo.Context) error {
	castListT := []db.Cast{}
	castListB := []apiInterface.Cast{}

	result := dbc.Find(&castListT)
	if result.Error != nil {
		return ctx.JSON(http.StatusInternalServerError, result.Error)
	}

	for _, castT := range castListT {
		castB := apiInterface.Cast{
			Id:   int(castT.ID),
			Name: castT.Name,
		}
		castListB = append(castListB, castB)
	}

	return ctx.JSON(http.StatusOK, castListB)
}

func (a ApiController) GetCast(ctx echo.Context, id int) error {
	castT := db.Cast{}
	castB := apiInterface.Cast{}

	conversionBind := func() {
		castB.Id = int(castT.ID)
		castB.Name = castT.Name
	}

	return GetGeneric(ctx, &castB, &castT, conversionBind, id)
}

func (a ApiController) UpdateCast(ctx echo.Context) error {
	castB := apiInterface.Cast{}
	castT := db.Cast{}

	conversionBind := func() {
		castT.ID = uint(castB.Id)
		castT.Name = castB.Name
	}

	return UpdateGeneric(ctx, &castB, &castT, conversionBind)
}

func (a ApiController) DeleteCast(ctx echo.Context, id int) error {
	castT := db.Cast{}
	return DeleteGeneric(ctx, &castT, id)
}
