#!/bin/ksh

SSH_OPTS="-T -o LogLevel=ERROR -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null"

# -------- JT1 (local) --------
df -gt /tmp | tail -1 | awk '{print "jt1",$1,$2,$3,$4,$5,$6}' OFS="|"
df -gt /orbixlog | tail -1 | awk '{print "jt1",$1,$2,$3,$4,$5,$6}' OFS="|"

# -------- JT2 --------
ssh $SSH_OPTS oracle1@ibmjtvsrvfra002 \
"df -gt /tmp | tail -1 | awk '{print \"jt2\",\$1,\$2,\$3,\$4,\$5,\$6}' OFS=\"|\"; \
 df -gt /orbixlog | tail -1 | awk '{print \"jt2\",\$1,\$2,\$3,\$4,\$5,\$6}' OFS=\"|\"; \
 df -gt /usr/apache-tomcat-7.0.42/logs | tail -1 | awk '{print \"jt2\",\$1,\$2,\$3,\$4,\$5,\$6}' OFS=\"|\""

# -------- JT3 --------
ssh $SSH_OPTS oracle1@ibmjtvsrvfra003 \
"df -gt /tmp | tail -1 | awk '{print \"jt3\",\$1,\$2,\$3,\$4,\$5,\$6}' OFS=\"|\"; \
 df -gt /usr/apache-tomcat-7.0.42/logs | tail -1 | awk '{print \"jt3\",\$1,\$2,\$3,\$4,\$5,\$6}' OFS=\"|\""

# -------- JT4 --------
ssh $SSH_OPTS oracle1@ibmjtvsrvfra004 \
"df -gt /tmp | tail -1 | awk '{print \"jt4\",\$1,\$2,\$3,\$4,\$5,\$6}' OFS=\"|\"; \
 df -gt /usr/apache-tomcat-7.0.42/logs | tail -1 | awk '{print \"jt4\",\$1,\$2,\$3,\$4,\$5,\$6}' OFS=\"|\""
