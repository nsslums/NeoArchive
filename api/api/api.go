package api

import (
	"log/slog"

	"github.com/irumaru/iass/api/apiController"
	"github.com/irumaru/iass/api/apiInterface"
	"github.com/labstack/echo/v4"
	"github.com/labstack/echo/v4/middleware"
	echomiddleware "github.com/oapi-codegen/echo-middleware"
)

var e *echo.Echo

// apiControllerのメソッドを実装

func init() {
	slog.Info("Initializing API Server")

	// Create API Server
	e = echo.New()

	// OpenAPIの仕様に沿っているかリクエストをバリデーションするミドルウェアを作成&追加
	swagger, err := apiInterface.GetSwagger()
	if err != nil {
		panic(err)
	}
	e.Use(echomiddleware.OapiRequestValidator(swagger))

	// LoggerとRecoverミドルウェアを追加
	e.Use(middleware.Logger())
	e.Use(middleware.Recover())

	api := apiController.ApiController{}
	apiInterface.RegisterHandlers(e, api)
}

func Start() {
	slog.Info("Starting API Server")
	e.Logger.Fatal(e.Start(":1323"))
}
