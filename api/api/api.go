package api

import (
	"fmt"
	"log/slog"

	"github.com/irumaru/iass/api/apiController"
	"github.com/irumaru/iass/api/apiInterface"
	"github.com/irumaru/iass/api/config"
	"github.com/labstack/echo/v4"
	"github.com/labstack/echo/v4/middleware"
	echomiddleware "github.com/oapi-codegen/echo-middleware"
)

var e *echo.Echo
var cfg *config.Define

// apiControllerのメソッドを実装

func init() {
	slog.Info("Initializing API Server")

	// Load Config
	cfg = config.Get()

	// Create API Server
	e = echo.New()

	// LoggerとRecoverミドルウェアを追加
	e.Use(middleware.Logger())
	e.Use(middleware.Recover())

	// CORSミドルウェアを追加
	// CORSミドルウェアはHTTP関連ミドルウェアやハンドラの前に追加する必要がある
	// Dev only, should be removed in production
	e.Use(middleware.CORS())

	// OpenAPIの仕様に沿っているかリクエストをバリデーションするミドルウェアを作成&追加
	swagger, err := apiInterface.GetSwagger()
	if err != nil {
		panic(err)
	}
	e.Use(echomiddleware.OapiRequestValidator(swagger))

	api := apiController.ApiController{}
	apiInterface.RegisterHandlers(e, api)
}

func Start() {
	slog.Info("Starting API Server")
	e.Logger.Fatal(e.Start(fmt.Sprintf(":%d", cfg.ApiPort)))
}
