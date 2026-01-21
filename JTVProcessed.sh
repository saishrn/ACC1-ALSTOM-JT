#!/bin/bash

JT_HOST="ibmjtvsrvfra000"

DB_LIST="
bapp_fra
bnap_fra
bogp_fra
lrvp_fra
mlnp_fra
pc30_fra
pgrs_fra
pgrz_fra
ploc_fra
ppcp_fra
prim_fra
psbb_fra
pscb_fra
serp_fra
ttsp_fra
"

for DB in $DB_LIST
do
    RESULT=$(ssh -T ${JT_HOST} 2>/dev/null <<EOF
sqlplus -s jtviewer/jtviewer@${DB} <<SQL
SET HEADING OFF
SET FEEDBACK OFF
SET PAGESIZE 0
SET VERIFY OFF
SET TRIMSPOOL ON

SELECT COUNT(*) FROM BTCV.CONVERSION_QUEUE_HISTORY
 WHERE STATUS='Successful'
   AND CONVERSION_SUBTYPE='NORMAL'
   AND TRUNC(MODIFIED_DATE)=TRUNC(SYSDATE)-1;

SELECT COUNT(*) FROM BTCV.CONVERSION_QUEUE_HISTORY
 WHERE STATUS='Successful'
   AND CONVERSION_SUBTYPE='EXTEND'
   AND TRUNC(MODIFIED_DATE)=TRUNC(SYSDATE)-1;

SELECT COUNT(*) FROM BTCV.CONVERSION_QUEUE_HISTORY
 WHERE STATUS='Successful'
   AND CONVERSION_SUBTYPE='NOLIMIT'
   AND TRUNC(MODIFIED_DATE)=TRUNC(SYSDATE)-1;

EXIT;
SQL
EOF
)

    NUMBERS=$(echo "$RESULT" | grep -E '^[[:space:]]*[0-9]+[[:space:]]*$')

    NORMAL=$(echo "$NUMBERS" | sed -n '1p')
    EXTEND=$(echo "$NUMBERS" | sed -n '2p')
    NOLIMIT=$(echo "$NUMBERS" | sed -n '3p')

    NORMAL=${NORMAL:-0}
    EXTEND=${EXTEND:-0}
    NOLIMIT=${NOLIMIT:-0}

    TOTAL=$((NORMAL + EXTEND + NOLIMIT))

    DB_NAME=$(echo "${DB%_*}" | tr '[:lower:]' '[:upper:]')

    echo "${DB_NAME}|${NORMAL}|${EXTEND}|${NOLIMIT}|${TOTAL}"
done
