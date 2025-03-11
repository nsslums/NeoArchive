package config

import (
	"log/slog"

	"github.com/kelseyhightower/envconfig"
)

var cfg Define

func loadConfig() error {
	slog.Info("Loading config")

	err := envconfig.Process("", &cfg)
	if err != nil {
		return err
	}

	return nil
}

func Get() *Define {
	return &cfg
}

func init() {
	slog.Info("Initializing config module")

	// Load the configuration
	err := loadConfig()
	if err != nil {
		slog.Error("Failed to load config: ", err)
		panic(err)
	}
}
