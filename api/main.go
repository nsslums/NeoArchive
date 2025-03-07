package main

import (
	"log/slog"

	"github.com/irumaru/iass/api/api"
)

func main() {
	slog.Info("Startup!")

	// Start API Server
	api.Start()
}
