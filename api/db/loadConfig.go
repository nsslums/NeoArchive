package db

import (
	"github.com/irumaru/iass/api/config"
)

var cfg *config.Define

func loadConfig() {
	cfg = config.Get()
}
