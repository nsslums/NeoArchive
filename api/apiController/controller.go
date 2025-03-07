package apiController

import (
	"net/http"

	"github.com/irumaru/iass/api/db"
	"github.com/labstack/echo/v4"
	"gorm.io/gorm"
)

type ApiController struct{}

var dbc *gorm.DB

func init() {
	// Get db connection
	dbc = db.Get()
}

// Helper

// 必須項目でない値が未入力の場合に、デフォルト値を返す
// https://github.com/oapi-codegen/oapi-codegen?tab=readme-ov-file#generating-nullable-types
func StringPtoV(p *string, defaultValue string) string {
	if p == nil {
		return defaultValue // Default value
	}
	return *p
}

func IntPtoV(p *uint64, defaultValue uint64) uint64 {
	if p == nil {
		return defaultValue // Default value
	}
	return *p
}

// Generic Create Entity
func CreateGeneric(ctx echo.Context, apiBody interface{}, tableModel interface{}, conversionBind func()) error {
	// Ger request body
	if err := ctx.Bind(apiBody); err != nil {
		return ctx.JSON(http.StatusBadRequest, err)
	}

	// Convert request body to table model
	conversionBind()

	// Insert into DB
	result := dbc.Create(tableModel)
	if result.Error != nil {
		return ctx.JSON(http.StatusInternalServerError, result.Error)
	}

	// Return response
	return ctx.JSON(http.StatusCreated, apiBody)
}

// Generic Delete Entity
func DeleteGeneric(ctx echo.Context, tableModel interface{}, id int) error {
	// Delete from DB
	result := dbc.Where("id = ?", id).Delete(&tableModel)
	if result.Error != nil {
		return ctx.JSON(http.StatusInternalServerError, result.Error)
	}

	return ctx.NoContent(http.StatusNoContent)
}
