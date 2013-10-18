#!/bin/bash
single_provision() {
    while read box; do
        echo "Provisioning '$box'." 1>&2
        vagrant ssh $box -c "sudo salt-call state.highstate"
    done 
}

# but run highstate one at a time
vagrant status | egrep --color=no '(master|client)' | awk '{ print $1 }' | single_provision