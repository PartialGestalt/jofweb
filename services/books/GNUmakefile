##
## JoF-RR makefile
##


##
## Section 1: Early setup
##
firstrule: all
PROJECT=jofrr
PROJECT_ROOT=../../..
SUBDIRS=

##
## Section 2: includes
##
include $(PROJECT_ROOT)/mk/config.mk
include $(PROJECT_ROOT)/mk/common.mk

##
## Section 2: Objects/targets
##
PHPSRCS=$(wildcard *.php.in)
PHPOBJS=$(PHPSRCS:%.php.in=%.php)
JSSRCS=$(wildcard *.js.in)
JSOBJS=$(JSSRCS:%.js.in=%.js)

OBJS=$(PHPOBJS) $(JSOBJS) $(HTMLOBJS)

##
## Section 3: Common rules
##
all: $(OBJS)
	$(DESCEND)

local-clean::
	@$(foreach cleanee,$(OBJS),$(call TOOL_CLEAN,$(cleanee)))

clean: local-clean
	$(DESCEND)

local-clobber: local-clean

clobber:: local-clobber
	$(DESCEND)

install: all
	$(call TOOL_MKDIR,$(CONTENT_ROOT),0755)
	@$(foreach obj,$(OBJS),$(call TOOL_INSTALL,$(obj),0644,$(CONTENT_ROOT)))
	$(DESCEND)

showcfg::
	@echo PHPOBJS=$(PHPOBJS)
	@echo PROJECT_ROOT=$(PROJECT_ROOT)
	@echo PROJECT_TOPDIR=$(PROJECT_TOPDIR)
	@echo PROJECT_CURDIR=$(PROJECT_CURDIR)
	@echo PROJECT_SUBDIR=$(PROJECT_SUBDIR)
	$(DESCEND)
