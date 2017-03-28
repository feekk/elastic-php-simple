<?php
namespace ElasticPhpSimple;

class Type{
    CONST EQUERY = "query";
    CONST ETYPE = "type";
    CONST EPHRASE = "phrase";
    CONST EMATCH = "match";
    CONST ENOTMATCH = "not_match";
    CONST EMATCHALL = "match_all";
    CONST EMUSTNOT = "must_not";
    CONST EBOOL = "bool";
    CONST ESHOULD = "should";
    CONST EWILDCARD = "wildcard";
    CONST EOFFSET = "from";
    CONST ESIZE = "size";
    CONST EORDER = "order";
    CONST ESOURCE = "_source";
    CONST EAGGS = "aggregations";
    CONST EMERTICS = "mertics";
    CONST EBUCKETS = "buckets";
    CONST ESORT = "sort";
    CONST EMUST = "must";
    CONST ERANGE = "range";
    CONST EINCUDELOWER = "include_lower";
    CONST EINCUDEUPPER = "include_upper";
    CONST ETO = "to";
    CONST EINDEX = "index";
    CONST EBODY = "body";
    CONST EMISSING = "missing";

    CONST ETERMS = "terms";
    CONST EINCLUDE = "include";
    CONST EEXCLUDE = "exclude";
    CONST EINCLUDES = "includes";
    CONST EEXCLUDES = "excludes";
    CONST EFORMAT = "format";
    CONST EINTERVAL = "interval"; //桶的间隔
    CONST EMINDOCCOUNT = "min_doc_count";
    CONST EEXTENDEDBOUNDS = "extended_bounds";
    CONST EMIN = "min";
    CONST EMAX = "max";
    CONST EHISTOGRAM = 'histogram';
    CONST EDATEHISTOGRAM = "date_histogram";
    CONST ESUM = "sum";
    CONST EAVG = "avg";
    CONST ECONT = "value_count";
    CONST EFIELD = "field";
    CONST ESCRIPT = "scrpit";
    CONST ETERM = "term";
    CONST ECARDINAL = "cardinality";
    CONST ERANGES = "ranges";
}


