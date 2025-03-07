package apiController

import (
	"net/http"

	"github.com/irumaru/iass/api/apiInterface"
	"github.com/irumaru/iass/api/db"
	"github.com/labstack/echo/v4"
)

func (a ApiController) CreateTag(ctx echo.Context) error {
	tagB := apiInterface.Tag{}
	tagT := db.Tag{}

	conversionBind := func() {
		tagT.Name = tagB.Name
	}

	return CreateGeneric(ctx, &tagB, &tagT, conversionBind)
}

func (a ApiController) GetTagList(ctx echo.Context) error {
	tagListT := []db.Tag{}
	tagListB := []apiInterface.Tag{}

	result := dbc.Find(&tagListT)
	if result.Error != nil {
		return ctx.JSON(http.StatusInternalServerError, result.Error)
	}

	for _, tagT := range tagListT {
		tagB := apiInterface.Tag{
			Name: tagT.Name,
		}
		tagListB = append(tagListB, tagB)
	}

	return ctx.JSON(http.StatusOK, tagListB)
}

func (a ApiController) UpdateTag(ctx echo.Context) error {
	return ctx.JSON(http.StatusMethodNotAllowed, "Method Not Allowed")
}

func (a ApiController) DeleteTag(ctx echo.Context, id int) error {
	tagT := db.Tag{}
	return DeleteGeneric(ctx, &tagT, id)
}
