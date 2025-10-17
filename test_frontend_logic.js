// Test the frontend follow-up question logic
function isFollowUpQuestion(currentQuestion) {
  // Simulate empty chat session
  const chatSessions = {
    current: [],
  };
  const currentSessionId = "current";

  if (
    !chatSessions[currentSessionId] ||
    chatSessions[currentSessionId].length === 0
  )
    return false;

  const followUpIndicators = [
    // English follow-up patterns
    /^(what about|how about|and|also|can you|what if|tell me more|continue|go on)/i,
    /(more|another|other|else|next|expand|further|additional)/i,
    /(that|this|it|they|them)\s/i,
    /^(show|list|find)\s+(more|other|another)/i,
    /(compare|versus|vs|difference)/i,

    // Filipino/Taglish follow-up patterns
    /^(paano|ano|at|pano|saka|tapos)/i,
    /(pa|din|rin|naman|lang)/i,
    /(yan|yun|iyan|iyon)/i,
  ];

  return followUpIndicators.some((pattern) =>
    pattern.test(currentQuestion.trim())
  );
}

// Test cases
const testCases = [
  "Princes Lyka",
  "Princes Lyka M Santos",
  "What about Princes Lyka",
  "Tell me more about Princes Lyka",
  "How about Princes Lyka",
];

console.log("Testing frontend follow-up question detection:");
console.log("================================================");

testCases.forEach((testCase) => {
  const isFollowUp = isFollowUpQuestion(testCase);
  console.log(`Input: '${testCase}'`);
  console.log(`Is follow-up: ${isFollowUp}`);
  console.log("-".repeat(30));
});
