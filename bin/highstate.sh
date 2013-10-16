#!/bin/bash
vagrant provision master
vagrant ssh master -c "sudo salt \\* --state-output=mixed state.highstate"