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

func IntPtoV(p *int, defaultValue int) int {
	if p == nil {
		return defaultValue // Default value
	}
	return *p
}

func IntVtoP(v int) *int {
	return &v
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

// Generic Get Entity
func GetGeneric(ctx echo.Context, apiBody interface{}, tableModel interface{}, conversionBind func(), id int) error {
	// Get from DB
	result := dbc.First(tableModel, id)
	if result.Error != nil {
		return ctx.JSON(http.StatusInternalServerError, result.Error)
	}

	// Convert table model to response body
	conversionBind()

	// Return response
	return ctx.JSON(http.StatusOK, apiBody)
}

func GetGenericPreload(ctx echo.Context, apiBody interface{}, tableModel interface{}, conversionBind func(), id int, preloadField string) error {
	// Get from DB
	result := dbc.Preload(preloadField).First(tableModel, id)
	if result.Error != nil {
		return ctx.JSON(http.StatusInternalServerError, result.Error)
	}

	// Convert table model to response body
	conversionBind()

	// Return response
	return ctx.JSON(http.StatusOK, apiBody)
}

// Generic Update Entity
func UpdateGeneric(ctx echo.Context, apiBody interface{}, tableModel interface{}, conversionBind func()) error {
	// Get request body
	if err := ctx.Bind(apiBody); err != nil {
		return ctx.JSON(http.StatusBadRequest, err)
	}

	// Convert request body to table model
	conversionBind()

	// Update DB
	result := dbc.Save(tableModel)
	if result.Error != nil {
		return ctx.JSON(http.StatusInternalServerError, result.Error)
	}

	// Return response
	return ctx.JSON(http.StatusOK, apiBody)
}

func UpdateAssociationGeneric(ctx echo.Context, apiBody interface{}, tableModel interface{}, associationTableModel interface{}, associationName string, conversionBind func()) error {
	// Get request body
	if err := ctx.Bind(apiBody); err != nil {
		return ctx.JSON(http.StatusBadRequest, err)
	}

	// Convert request body to table model
	conversionBind()

	// Update DB
	err := dbc.Transaction(func(tx *gorm.DB) error {
		if err := dbc.Model(tableModel).Updates(tableModel).Error; err != nil {
			return err
		}

		if err := dbc.Model(tableModel).Association(associationName).Replace(associationTableModel); err != nil {
			return err
		}

		return nil
	})
	if err != nil {
		return ctx.JSON(http.StatusInternalServerError, err)
	}

	// Return response
	return ctx.JSON(http.StatusOK, apiBody)
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
