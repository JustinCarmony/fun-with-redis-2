#!/bin/bash

# Setup

MAX_PROCS=20
 
parallel_provision() {
    while read box; do
        echo "Provisioning '$box'. Output will be in: log/$box.out.txt" 1>&2
        echo $box
    done | xargs -P $MAX_PROCS -I"BOXNAME" \
        sh -c 'vagrant provision BOXNAME '
}

# but run provision tasks in parallel
vagrant status | egrep --color=no '(master|client)' | awk '{ print $1 }' | parallel_provision

# I Don't know why, but the master server's minion is having a hard time connecting to the 
# Salt Master, so run the provision on the master to re-connect it.
#vagrant provision master
