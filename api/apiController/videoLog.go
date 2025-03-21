package apiController

import (
	"net/http"

	"github.com/labstack/echo/v4"
)

func (a ApiController) CreateVideoLog(ctx echo.Context) error {
	return ctx.JSON(http.StatusMethodNotAllowed, "Method Not Allowed")
}

func (a ApiController) GetVideoLog(ctx echo.Context, id int) error {
	return ctx.JSON(http.StatusMethodNotAllowed, "Method Not Allowed")
}

func (a ApiController) UpdateVideoLog(ctx echo.Context) error {
	return ctx.JSON(http.StatusMethodNotAllowed, "Method Not Allowed")
}

func (a ApiController) DeleteVideoLog(ctx echo.Context, id int) error {
	return ctx.JSON(http.StatusMethodNotAllowed, "Method Not Allowed")
}
