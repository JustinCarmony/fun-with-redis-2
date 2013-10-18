#!/bin/bash
vagrant ssh master -c "sudo salt '*' cmd.run 'service supervisor stop; service supervisor start;'"