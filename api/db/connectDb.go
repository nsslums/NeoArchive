package db

import (
	"fmt"
	"log/slog"
	"os"
	"path/filepath"

	"gorm.io/driver/mysql"
	"gorm.io/driver/sqlite"
	"gorm.io/gorm"
)

var db *gorm.DB

func connectDb() error {
	slog.Info("Connecting to the database")
	// Connect to the database based on the configuration
	var dl gorm.Dialector

	switch cfg.DbMode {
	case "sqlite":
		slog.Info("Use SQLite mode")
		// Create the directory if it does not exist
		err := os.MkdirAll(filepath.Dir(cfg.DbSqlitePath), os.ModePerm)
		if err != nil {
			return fmt.Errorf("failed to create the sqlite file directory: %w", err)
		}

		dl = sqlite.Open(cfg.DbSqlitePath)
	case "mysql":
		slog.Info("Use MySQL mode")
		dl = mysql.Open(cfg.DbMysqlDsn)
	default:
		return fmt.Errorf("invalid database mode: %s", cfg.DbMode)
	}

	var err error
	db, err = gorm.Open(dl, &gorm.Config{})
	if err != nil {
		return fmt.Errorf("failed to connect to the database: %w", err)
	}

	return nil
}

func Get() *gorm.DB {
	return db
}
