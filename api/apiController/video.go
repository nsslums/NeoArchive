package apiController

import (
	"github.com/irumaru/iass/api/apiInterface"
	"github.com/irumaru/iass/api/db"
	"github.com/labstack/echo/v4"
)

func (a ApiController) CreateVideo(ctx echo.Context) error {
	videoB := apiInterface.Video{}
	videoT := db.Video{}

	conversionBind := func() {
		videoT.BroadcastTime = videoB.BroadcastTime
		videoT.Length = uint(*videoB.Length)
	}

	return CreateGeneric(ctx, &videoB, &videoT, conversionBind)
}

func (a ApiController) GetVideo(ctx echo.Context, id int) error {
	videoT := db.Video{}
	videoB := apiInterface.Video{}

	conversionBind := func() {
		videoB.Id = int(videoT.ID)
		videoB.BroadcastTime = videoT.BroadcastTime
		*videoB.Length = int(videoT.Length)
	}

	return GetGeneric(ctx, &videoB, &videoT, conversionBind, id)
}

func (a ApiController) UpdateVideo(ctx echo.Context) error {
	videoB := apiInterface.Video{}
	videoT := db.Video{}

	conversionBind := func() {
		videoT.ID = uint(videoB.Id)
		videoT.BroadcastTime = videoB.BroadcastTime
		videoT.Length = uint(*videoB.Length)
	}

	return UpdateGeneric(ctx, &videoB, &videoT, conversionBind)
}

func (a ApiController) DeleteVideo(ctx echo.Context, id int) error {
	videoT := db.Video{}
	return DeleteGeneric(ctx, &videoT, id)
}
