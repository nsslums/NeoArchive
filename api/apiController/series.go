package apiController

import (
	"net/http"

	"github.com/irumaru/iass/api/apiInterface"
	"github.com/irumaru/iass/api/db"
	"github.com/labstack/echo/v4"
	"gorm.io/gorm"
)

func arrayToTagIds(tagsId *[]int) []db.Tag {
	TagIdsT := []db.Tag{}
	for _, tagId := range *tagsId {
		TagIdsT = append(TagIdsT, db.Tag{ID: uint(tagId)})
	}
	return TagIdsT
}

func tagIdsToArray(tagIdsT []db.Tag) *[]int {
	tagsIds := []int{}
	for _, tag := range tagIdsT {
		tagsIds = append(tagsIds, int(tag.ID))
	}
	return &tagsIds
}

func (a ApiController) CreateSeries(ctx echo.Context) error {
	seriesB := apiInterface.Series{}
	seriesT := db.Series{}

	conversionBind := func() {
		seriesT.Title = seriesB.Title
		seriesT.Tag = arrayToTagIds(seriesB.TagsId)
		//seriesT.Tag = arrayToIds(seriesB.TagsId).([]db.Tag)
	}

	return CreateGeneric(ctx, &seriesB, &seriesT, conversionBind)
}

func (a ApiController) GetSeriesList(ctx echo.Context, params apiInterface.GetSeriesListParams) error {
	q := params.Q

	seriesListT := []db.Series{}
	seriesListB := []apiInterface.Series{}

	var result *gorm.DB
	if q == nil {
		result = dbc.Preload("Tag").Find(&seriesListT)
	} else {
		result = dbc.Preload("Tag").Where("title LIKE %?%", *q).Find(&seriesListT)
	}
	if result.Error != nil {
		return ctx.JSON(http.StatusInternalServerError, result.Error)
	}

	for _, seriesT := range seriesListT {
		seriesB := apiInterface.Series{
			Id:     int(seriesT.ID),
			Title:  seriesT.Title,
			TagsId: tagIdsToArray(seriesT.Tag),
		}
		seriesListB = append(seriesListB, seriesB)
	}

	return ctx.JSON(http.StatusOK, seriesListB)
}

func (a ApiController) GetSeries(ctx echo.Context, id int) error {
	seriesT := db.Series{}
	seriesB := apiInterface.Series{}

	conversionBind := func() {
		seriesB.Id = int(seriesT.ID)
		seriesB.Title = seriesT.Title
		seriesB.TagsId = tagIdsToArray(seriesT.Tag)
	}

	return GetGenericPreload(ctx, &seriesB, &seriesT, conversionBind, id, "Tag")
}

func (a ApiController) UpdateSeries(ctx echo.Context) error {
	seriesB := apiInterface.Series{}
	seriesT := db.Series{}

	conversionBind := func() {
		seriesT.ID = uint(seriesB.Id)
		seriesT.Title = seriesB.Title
		seriesT.Tag = arrayToTagIds(seriesB.TagsId)
	}

	return UpdateAssociationGeneric(ctx, &seriesB, &seriesT, &seriesT.Tag, "Tag", conversionBind)
}

func (a ApiController) DeleteSeries(ctx echo.Context, id int) error {
	seriesT := db.Series{}
	return DeleteGeneric(ctx, &seriesT, id)
}
