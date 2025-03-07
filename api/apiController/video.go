package apiController

import (
	"net/http"

	"github.com/labstack/echo/v4"
)

func (a ApiController) CreateVideo(ctx echo.Context) error {
	return ctx.JSON(http.StatusMethodNotAllowed, "Method Not Allowed")
}

func (a ApiController) GetVideo(ctx echo.Context, id int) error {
	return ctx.JSON(http.StatusMethodNotAllowed, "Method Not Allowed")
}

func (a ApiController) UpdateVideo(ctx echo.Context) error {
	return ctx.JSON(http.StatusMethodNotAllowed, "Method Not Allowed")
}

func (a ApiController) DeleteVideo(ctx echo.Context, id int) error {
	return ctx.JSON(http.StatusMethodNotAllowed, "Method Not Allowed")
}
