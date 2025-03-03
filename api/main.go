package main

import (
	"log/slog"

	"github.com/irumaru/iass/api/api"
	"github.com/irumaru/iass/api/db"
)

func main() {
	slog.Info("Startup!")

	// Start API Server
	api.Start()

	// Debug
	db := db.Get()
	slog.Info("DB: ", db)
}
