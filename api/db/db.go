package db

import (
	"log/slog"
)

func init() {
	slog.Info("Initializing database module")

	loadConfig()

	err := connectDb()
	if err != nil {
		slog.Error("Database module error, ", err)
		panic(err)
	}

	migrate()
}
